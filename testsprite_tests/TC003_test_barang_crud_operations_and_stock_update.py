import requests
from requests.sessions import Session

BASE_URL = "http://localhost:8000"
LOGIN_URL = f"{BASE_URL}/login"
BARANG_URL = f"{BASE_URL}/barang"
BARANG_BULK_DELETE_URL = f"{BARANG_URL}/bulk/delete"


def test_barang_crud_operations_and_stock_update():
    session = Session()
    # Removed application/json Accept header as login expects form and redirects
    timeout = 30

    # 1. Login to get authenticated session
    login_data = {
        'username': 'admin',
        'password': 'admin123',
    }
    resp = session.post(LOGIN_URL, data=login_data, timeout=timeout, allow_redirects=False)
    assert resp.status_code == 302 and '/dashboard' in resp.headers.get('Location', ''), \
        "Login failed or unexpected redirect"
    # Session cookie is maintained automatically by requests.Session

    created_barang_ids = []

    def create_barang(nama_barang, kategori='ATK', satuan='pcs', stok=10, stok_minimum=2, catatan=None):
        data = {
            'nama_barang': nama_barang,
            'kategori': kategori,
            'satuan': satuan,
            'stok': stok,
            'stok_minimum': stok_minimum,
        }
        if catatan is not None:
            data['catatan'] = catatan
        resp = session.post(BARANG_URL, data=data, timeout=timeout, allow_redirects=False)
        return resp

    def get_barang_detail(barang_id):
        resp = session.get(f"{BARANG_URL}/{barang_id}", timeout=timeout)
        return resp

    def update_stok(barang_id, stok_baru, catatan=None):
        data = {'stok_baru': stok_baru}
        if catatan is not None:
            data['catatan'] = catatan
        resp = session.post(f"{BARANG_URL}/{barang_id}/update-stok", data=data, timeout=timeout, allow_redirects=False)
        return resp

    def delete_barang(barang_id):
        resp = session.delete(f"{BARANG_URL}/{barang_id}", timeout=timeout, allow_redirects=False)
        return resp

    def bulk_delete_barang(ids):
        data = [('ids[]', str(i)) for i in ids]
        resp = session.delete(BARANG_BULK_DELETE_URL, data=data, timeout=timeout)
        return resp

    try:
        # --- CREATE NEW ITEM - should succeed ---
        new_name = "Test Item A"
        resp_create = create_barang(new_name)
        assert resp_create.status_code == 302 and '/barang' in resp_create.headers.get('Location', ''), \
            "Failed to create new barang item"
        # Extract created barang id by fetching list and searching by name (not ideal - but no direct id returned)
        resp_list = session.get(BARANG_URL, timeout=timeout)
        assert resp_list.status_code == 200
        found_id = None
        items = resp_list.text
        # crude parse: find /barang/{id} with new_name in page
        # Better to get json but assumed not present, so parse html for hrefs
        import re
        matches = re.findall(r'/barang/(\d+)', items)
        for m in matches:
            detail_resp = get_barang_detail(m)
            if detail_resp.status_code == 200 and new_name in detail_resp.text:
                found_id = m
                break
        assert found_id is not None, "Created barang id not found after creation"
        created_barang_ids.append(found_id)

        barang_id = found_id

        # --- CREATE DUPLICATE NAME - should fail with 422 ---
        resp_dup = create_barang(new_name)
        assert resp_dup.status_code == 422, "Duplicate barang creation did not return 422"
        assert 'Nama barang sudah digunakan' in resp_dup.text, "Expected duplicate name error message"

        # --- UPDATE STOCK with VALID value ---
        valid_new_stock = 15
        resp_update_stock = update_stok(barang_id, valid_new_stock, catatan="Stock updated for testing")
        # Can be 302 or 200 per PRD
        assert resp_update_stock.status_code in (200, 302), "Valid stock update failed"
        # Verify stock updated from detail page
        detail_after_update = get_barang_detail(barang_id)
        assert detail_after_update.status_code == 200
        assert f'Stok: {valid_new_stock}' in detail_after_update.text or f'"stok":{valid_new_stock}' in detail_after_update.text, \
            "Stock not updated correctly in detail"

        # --- UPDATE STOCK with INVALID NEGATIVE value - should fail 422 ---
        respons_neg = update_stok(barang_id, -5)
        assert respons_neg.status_code == 422, "Negative stock update did not return 422"
        assert 'Stok tidak boleh negatif' in respons_neg.text, "Expected negative stock error message"

        # --- DELETE ITEM WITHOUT TRANSACTIONS - should succeed with 302 redirect ---
        # First create item with no transactions for delete test
        resp_create_del = create_barang("DeleteMeItem", stok=0)
        assert resp_create_del.status_code == 302
        # Find ID
        resp_list = session.get(BARANG_URL, timeout=timeout)
        delete_id = None
        matches = re.findall(r'/barang/(\d+)', resp_list.text)
        for m in matches:
            detail_resp = get_barang_detail(m)
            if detail_resp.status_code == 200 and "DeleteMeItem" in detail_resp.text:
                delete_id = m
                break
        assert delete_id is not None, "Delete test barang id not found"

        resp_delete = delete_barang(delete_id)
        assert resp_delete.status_code == 302 and '/barang' in resp_delete.headers.get('Location', ''), \
            "Deleting barang without transaksi failed"

        # --- DELETE ITEM WITH RELATED TRANSACTIONS - expect 400 or 422 error ---
        # To create a transaction, first update stock which creates transaction per PRD
        # So create another barang for this test
        resp_create_tx = create_barang("WithTransactionItem", stok=5)
        assert resp_create_tx.status_code == 302
        # Find ID
        resp_list = session.get(BARANG_URL, timeout=timeout)
        tx_barang_id = None
        for m in re.findall(r'/barang/(\d+)', resp_list.text):
            detail_resp = get_barang_detail(m)
            if detail_resp.status_code == 200 and "WithTransactionItem" in detail_resp.text:
                tx_barang_id = m
                break
        assert tx_barang_id is not None, "Transaction barang id not found"

        created_barang_ids.append(tx_barang_id)
        # Update stock which creates transaction
        resp_update_stock_tx = update_stok(tx_barang_id, 10, catatan="Add stock for transaction test")
        assert resp_update_stock_tx.status_code in (200, 302), "Stock update for transaction test failed"

        # Now try to delete barang with transactions
        resp_del_tx = delete_barang(tx_barang_id)
        assert resp_del_tx.status_code in (400, 422), "Deleting barang with transactions did not return error"
        assert ('Barang tidak dapat dihapus karena memiliki riwayat transaksi' in resp_del_tx.text), \
            "Expected error message on deleting barang with transaksi"

        # --- BULK DELETE ---
        # Create multiple barang items - some deletable, some with transactions
        bulk_names = ["BulkDelete1", "BulkDelete2", "BulkDeleteTx"]
        bulk_ids = []
        for name in bulk_names:
            r = create_barang(name, stok=0 if 'Tx' not in name else 5)
            assert r.status_code == 302
        # Find their IDs
        resp_list = session.get(BARANG_URL, timeout=timeout)
        for m in re.findall(r'/barang/(\d+)', resp_list.text):
            detail_resp = get_barang_detail(m)
            if detail_resp.status_code != 200:
                continue
            detail_text = detail_resp.text
            for bname in bulk_names:
                if bname in detail_text and m not in bulk_ids:
                    bulk_ids.append(m)
                    break
        # We expect 3 in bulk_ids
        assert len(bulk_ids) >= 3, "Failed to retrieve bulk barang IDs"

        # Create transaction for BulkDeleteTx
        tx_id = None
        for m in bulk_ids:
            d_r = get_barang_detail(m)
            if "BulkDeleteTx" in d_r.text:
                tx_id = m
                break
        assert tx_id is not None

        # Add stock for BulkDeleteTx to create transaction
        resp_update_stock_bulk_tx = update_stok(tx_id, 10, catatan="Setup transaction for bulk delete test")
        assert resp_update_stock_bulk_tx.status_code in (200, 302)

        # Bulk delete all three
        resp_bulk_delete = bulk_delete_barang(bulk_ids)
        assert resp_bulk_delete.status_code == 200
        # Expect response JSON or text summary with items deleted and failed
        try:
            summary = resp_bulk_delete.json()
            assert 'items_deleted' in summary and 'items_failed' in summary
            # items_failed should contain tx_id
            assert any(str(tx_id) == str(i) for i in summary['items_failed']), "Expected transaction item in failed deletes"
        except Exception:
            # Maybe text response - check keys in text
            text_lower = resp_bulk_delete.text.lower()
            assert 'items deleted' in text_lower and 'items failed' in text_lower, "Bulk delete summary missing in response"

    finally:
        # Cleanup created barang items that still exist (skip those deleted)
        for bid in created_barang_ids:
            try:
                resp = delete_barang(bid)
                if resp.status_code not in (302, 400, 422):
                    pass  # Ignore non-standard status
            except Exception:
                pass


test_barang_crud_operations_and_stock_update()
