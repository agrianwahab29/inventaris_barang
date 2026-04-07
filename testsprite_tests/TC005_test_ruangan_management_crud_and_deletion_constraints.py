import requests

BASE_URL = "http://localhost:8000"
LOGIN_URL = f"{BASE_URL}/login"
RUANGAN_URL = f"{BASE_URL}/ruangan"
TIMEOUT = 30

ADMIN_USERNAME = "admin"
ADMIN_PASSWORD = "admin123"

session = requests.Session()

def login_as_admin():
    # Step 1: GET /login to get cookies, session and CSRF token
    resp = session.get(LOGIN_URL, timeout=TIMEOUT)
    resp.raise_for_status()

    # Extract CSRF token from login form
    import re
    match = re.search(r'name="_token" value="([^"]+)"', resp.text)
    assert match, "CSRF token not found in login form"
    csrf_token = match.group(1)

    # Step 2: POST /login with valid credentials and CSRF token
    login_data = {
        "_token": csrf_token,
        "username": ADMIN_USERNAME,
        "password": ADMIN_PASSWORD
    }
    resp = session.post(LOGIN_URL, data=login_data, allow_redirects=False, timeout=TIMEOUT)
    assert resp.status_code == 302, f"Login failed, expected 302 redirect, got {resp.status_code}"
    assert resp.headers.get("location") == "/dashboard", f"Unexpected redirect location after login: {resp.headers.get('location')}"

    # After login, session should have cookies set for authenticated requests

def test_ruangan_management_crud_and_deletion_constraints():
    login_as_admin()

    # Create new ruangan
    ruangan_data = {
        "nama_ruangan": "Test Ruangan TC005",
        "keterangan": "Ruangan created for test TC005"
    }
    resp = session.post(RUANGAN_URL, data=ruangan_data, allow_redirects=False, timeout=TIMEOUT)
    # Expect 302 redirect to /ruangan on success
    assert resp.status_code == 302, f"Failed to create ruangan, expected 302, got {resp.status_code}"
    assert resp.headers.get("location") == "/ruangan", "Unexpected redirect location after ruangan creation"

    # Fetch list of ruangan to find the ID of created room by searching name
    list_resp = session.get(RUANGAN_URL, timeout=TIMEOUT)
    list_resp.raise_for_status()
    ruangan_id = None
    try:
        # Assuming the list API returns HTML, parse as text for the new room name and ID link
        # We will do a simple search as no JSON API is defined for list
        # The ID is typically in URL /ruangan/{id} or /ruangan/{id}/edit as href
        # We'll look for "Test Ruangan TC005" in response text and extract ID from link
        import re
        pattern = r'<a href="/ruangan/(\d+)[^"]*">Test Ruangan TC005</a>'
        match = re.search(pattern, list_resp.text)
        if match:
            ruangan_id = match.group(1)
    except Exception:
        pass

    assert ruangan_id is not None, "Failed to find created ruangan ID in list"

    try:
        # Update ruangan (PUT /ruangan/{ruangan})
        update_url = f"{RUANGAN_URL}/{ruangan_id}"
        updated_data = {
            "nama_ruangan": "Test Ruangan TC005 Updated",
            "keterangan": "Updated keterangan for test TC005"
        }
        resp = session.put(update_url, data=updated_data, allow_redirects=False, timeout=TIMEOUT)
        assert resp.status_code == 302, f"Failed to update ruangan, expected 302, got {resp.status_code}"
        assert resp.headers.get("location") == "/ruangan", "Unexpected redirect location after ruangan update"

        # Attempt to delete ruangan while ensuring validation of deletion constraint:
        # First, test deleting ruangan with no related transaksi - should succeed
        del_resp = session.delete(update_url, allow_redirects=False, timeout=TIMEOUT)
        # Success expected as no transaction linked
        assert del_resp.status_code == 302, f"Failed to delete ruangan without transaksi, expected 302, got {del_resp.status_code}"
        assert del_resp.headers.get("location") == "/ruangan", "Unexpected redirect location after ruangan deletion"

        # Now create another ruangan for deletion constraint test (will associate transaksi)
        create_resp = session.post(RUANGAN_URL, data=ruangan_data, allow_redirects=False, timeout=TIMEOUT)
        assert create_resp.status_code == 302
        # Fetch ID again
        list_resp2 = session.get(RUANGAN_URL, timeout=TIMEOUT)
        list_resp2.raise_for_status()
        ruangan_id2 = None
        match2 = re.search(pattern, list_resp2.text)
        if match2:
            ruangan_id2 = match2.group(1)
        assert ruangan_id2 is not None, "Failed to find second created ruangan ID"

        # Now create a barang to create transaksi linked to ruangan
        barang_data = {
            "nama_barang": "TC005 Barang for transaksi",
            "kategori": "lainnya",
            "satuan": "pcs",
            "stok": 10,
            "stok_minimum": 1,
            "catatan": "Created for TC005 transaksi link"
        }
        barang_resp = session.post(f"{BASE_URL}/barang", data=barang_data, allow_redirects=False, timeout=TIMEOUT)
        assert barang_resp.status_code == 302
        # Find barang_id from redirect or list
        barang_list_resp = session.get(f"{BASE_URL}/barang", timeout=TIMEOUT)
        barang_list_resp.raise_for_status()
        barang_id = None
        try:
            pattern_barang = r'<a href="/barang/(\d+)[^"]*>TC005 Barang for transaksi</a>'
            mbr = re.search(pattern_barang, barang_list_resp.text)
            if mbr:
                barang_id = mbr.group(1)
        except Exception:
            pass
        assert barang_id is not None, "Failed to find barang id for transaksi"

        # Create transaksi linked to ruangan, which should block ruangan deletion
        transaksi_data = {
            "barang_id": barang_id,
            "tipe": "masuk",
            "jumlah_masuk": 5,
            "tanggal": "2026-04-04",
            "ruangan_id": ruangan_id2,
            "nama_pengambil": "Tester TC005",
            "tipe_pengambil": "admin",
            "keterangan": "Transaksi for deletion constraint test"
        }
        transaksi_resp = session.post(f"{BASE_URL}/transaksi", data=transaksi_data, allow_redirects=False, timeout=TIMEOUT)
        assert transaksi_resp.status_code == 302, f"Failed to create transaksi, expected 302, got {transaksi_resp.status_code}"

        # Now attempt to delete ruangan linked to transaksi -> expect 400 or 422 with validation error message
        del_resp2 = session.delete(f"{RUANGAN_URL}/{ruangan_id2}", allow_redirects=False, timeout=TIMEOUT)
        assert del_resp2.status_code in (400, 422), f"Expected 400 or 422 when deleting ruangan with transaksi, got {del_resp2.status_code}"
        text_lower = del_resp2.text.lower()
        # Validate error message presence in response body text
        assert "ruangan tidak dapat dihapus" in text_lower, "Expected error message about ruangan not deletable due to transaksi"

    finally:
        # Cleanup: delete second ruangan and barang (including related transaksi)
        # First delete transaksi for ruangan_id2
        transaksi_list_resp = session.get(f"{BASE_URL}/transaksi", timeout=TIMEOUT)
        transaksi_list_resp.raise_for_status()
        import re
        try:
            transaksi_ids = re.findall(r'/transaksi/(\d+)"', transaksi_list_resp.text)
            for tid in transaksi_ids:
                # Fetch transaksi detail to check ruangan_id
                detail_resp = session.get(f"{BASE_URL}/transaksi/{tid}", timeout=TIMEOUT)
                detail_resp.raise_for_status()
                if f'/ruangan/{ruangan_id2}' in detail_resp.text:
                    # Delete transaksi
                    del_tr = session.delete(f"{BASE_URL}/transaksi/{tid}", allow_redirects=False, timeout=TIMEOUT)
                    if del_tr.status_code not in (302, 200):
                        # Attempt force delete or log failure
                        pass
        except Exception:
            pass

        # Delete ruangan 2 after deleting transaksi
        if 'ruangan_id2' in locals():
            session.delete(f"{RUANGAN_URL}/{ruangan_id2}", allow_redirects=False, timeout=TIMEOUT)

        # Delete barang created for transaksi test
        if 'barang_id' in locals():
            session.delete(f"{BASE_URL}/barang/{barang_id}", allow_redirects=False, timeout=TIMEOUT)

test_ruangan_management_crud_and_deletion_constraints()
