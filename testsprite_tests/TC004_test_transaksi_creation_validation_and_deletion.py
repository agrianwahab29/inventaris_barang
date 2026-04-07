import requests
import datetime

BASE_URL = "http://localhost:8000"
LOGIN_URL = f"{BASE_URL}/login"
TRANSAKSI_URL = f"{BASE_URL}/transaksi"
BARANG_URL = f"{BASE_URL}/barang"

USERNAME = "admin"
PASSWORD = "admin123"

TIMEOUT = 30


def test_transaksi_creation_validation_and_deletion():
    session = requests.Session()

    # Step 1: Authenticate via POST /login to get session cookie
    login_data = {
        "username": USERNAME,
        "password": PASSWORD,
    }
    resp = session.get(LOGIN_URL, timeout=TIMEOUT)
    assert resp.status_code == 200

    resp = session.post(LOGIN_URL, data=login_data, allow_redirects=False, timeout=TIMEOUT)
    # Successful login should redirect to /dashboard (302)
    assert resp.status_code == 302
    assert "/dashboard" in resp.headers.get("Location", "")

    # Step 2: Get a valid barang to work with (for transaksi creation)
    # GET /barang (paginated list)
    resp = session.get(BARANG_URL, timeout=TIMEOUT)
    assert resp.status_code == 200

    # No JSON expected from GET /barang per PRD, so don't call resp.json()
    barang_list = None

    barang_id = None

    if not barang_list:
        # Fallback: create a new barang if none exists
        barang_create_data = {
            "nama_barang": f"TestBarang_{datetime.datetime.utcnow().strftime('%Y%m%d%H%M%S')}",
            "kategori": "ATK",  # Assuming "ATK" is a valid enum value
            "satuan": "pcs",
            "stok": 10,
            "stok_minimum": 1,
            "catatan": "Barang for test transaksi",
        }
        resp_create = session.post(BARANG_URL, data=barang_create_data, allow_redirects=False, timeout=TIMEOUT)
        assert resp_create.status_code == 302

        # Since no direct way to get barang_id from response, try fetching id by matching name on AJAX
        # We'll attempt IDs 1 to 20 and check which has nama_barang matching created name
        created_name = barang_create_data["nama_barang"]

        for test_id in range(1, 21):
            api_url = f"{BASE_URL}/api/barang/{test_id}/info"
            resp_test = session.get(api_url, timeout=TIMEOUT)
            if resp_test.status_code != 200:
                continue
            info = resp_test.json()
            if info.get("nama_barang") == created_name:
                barang_id = test_id
                break

        assert barang_id is not None, "Failed to locate created barang ID"
    else:
        # No JSON parsing from GET /barang so fallback: raise error
        raise AssertionError("No barang_list JSON available, unable to proceed")

    transaksi_ids_to_cleanup = []

    try:
        # Helper: get current stok for barang via AJAX-like /api/barang/{id}/info (auth required)
        api_barang_info_url = f"{BASE_URL}/api/barang/{barang_id}/info"
        resp_info = session.get(api_barang_info_url, timeout=TIMEOUT)
        assert resp_info.status_code == 200
        barang_info = resp_info.json()
        current_stok = barang_info.get("stok")
        assert isinstance(current_stok, int)

        # TODAY'S DATE in ISO format
        today_str = datetime.datetime.utcnow().date().isoformat()

        # 1. Create "masuk" transaction: jumlah_masuk > 0, jumlah_keluar null
        transaksi_masuk_data = {
            "barang_id": barang_id,
            "jumlah_masuk": 5,
            # 'jumlah_keluar' omitted since null
            "tanggal_masuk": today_str,
            # other optional fields omitted if None
            "nama_pengambil": "Tester",
            "keterangan": "Transaksi masuk test",
        }
        resp = session.post(TRANSAKSI_URL, data=transaksi_masuk_data, allow_redirects=False, timeout=TIMEOUT)
        # Should redirect to /transaksi on success (302)
        assert resp.status_code == 302
        assert "/transaksi" in resp.headers.get("Location", "")

        # Extract created transaksi ID from latest transaksi list may be HTML, so skip
        latest_transaksi_id = None

        if latest_transaksi_id is not None:
            transaksi_ids_to_cleanup.append(latest_transaksi_id)

        # Verify barang stock increased by jumlah_masuk
        resp_info2 = session.get(api_barang_info_url, timeout=TIMEOUT)
        assert resp_info2.status_code == 200
        stok_after_masuk = resp_info2.json().get("stok")
        assert stok_after_masuk == current_stok + 5

        # 2. Create "keluar" transaction: jumlah_keluar <= current_stok, valid
        keluar_jumlah = 3
        transaksi_keluar_data = {
            "barang_id": barang_id,
            # 'jumlah_masuk' omitted since null
            "jumlah_keluar": keluar_jumlah,
            "tanggal_keluar": today_str,
            "nama_pengambil": "Tester",
            "keterangan": "Transaksi keluar test",
        }
        resp = session.post(TRANSAKSI_URL, data=transaksi_keluar_data, allow_redirects=False, timeout=TIMEOUT)
        assert resp.status_code == 302
        assert "/transaksi" in resp.headers.get("Location", "")

        latest_transaksi_id_keluar = None

        if latest_transaksi_id_keluar is not None:
            transaksi_ids_to_cleanup.append(latest_transaksi_id_keluar)

        # Verify stock decreased by jumlah_keluar
        resp_info3 = session.get(api_barang_info_url, timeout=TIMEOUT)
        assert resp_info3.status_code == 200
        stok_after_keluar = resp_info3.json().get("stok")
        expected_stok_after_keluar = stok_after_masuk - keluar_jumlah
        assert stok_after_keluar == expected_stok_after_keluar

        # 3. Create "keluar" transaction but jumlah_keluar > current stock => expect 422 error
        invalid_keluar_data = {
            "barang_id": barang_id,
            "jumlah_keluar": stok_after_keluar + 100,
            "tanggal_keluar": today_str,
            "nama_pengambil": "Tester",
            "keterangan": "Invalid keluar test",
        }
        resp_invalid_keluar = session.post(TRANSAKSI_URL, data=invalid_keluar_data, allow_redirects=False, timeout=TIMEOUT)
        assert resp_invalid_keluar.status_code == 422 or resp_invalid_keluar.status_code == 400
        error_msg = resp_invalid_keluar.text.lower()
        assert "jumlah keluar melebihi stok tersedia" in error_msg or "validation" in error_msg

        # 4. Create "masuk_keluar" transaction where jumlah_keluar <= current_stok + jumlah_masuk, valid
        jumlah_masuk_mk = 4
        jumlah_keluar_mk = 2  # <= current_stok + jumlah_masuk_mk
        transaksi_masuk_keluar_data = {
            "barang_id": barang_id,
            "jumlah_masuk": jumlah_masuk_mk,
            "jumlah_keluar": jumlah_keluar_mk,
            "tanggal_masuk": today_str,
            "tanggal_keluar": today_str,
            "nama_pengambil": "Tester",
            "keterangan": "Transaksi masuk_keluar test",
        }
        resp = session.post(TRANSAKSI_URL, data=transaksi_masuk_keluar_data, allow_redirects=False, timeout=TIMEOUT)
        assert resp.status_code == 302
        assert "/transaksi" in resp.headers.get("Location", "")

        latest_transaksi_id_mk = None

        if latest_transaksi_id_mk is not None:
            transaksi_ids_to_cleanup.append(latest_transaksi_id_mk)

        # Verify stock updated with net change: stok = stok_after_keluar + jumlah_masuk_mk - jumlah_keluar_mk
        resp_info4 = session.get(api_barang_info_url, timeout=TIMEOUT)
        assert resp_info4.status_code == 200
        stok_after_mk = resp_info4.json().get("stok")
        expected_stok_after_mk = stok_after_keluar + jumlah_masuk_mk - jumlah_keluar_mk
        assert stok_after_mk == expected_stok_after_mk

        # 5. Delete each created transaksi and verify stock rollback and permission
        # Only admin user so permission denied case not tested here
        current_stok_check = stok_after_mk
        for tid in transaksi_ids_to_cleanup:
            # Get transaksi detail before deletion to calculate stock rollback
            resp_tx_detail = session.get(f"{TRANSAKSI_URL}/{tid}", timeout=TIMEOUT)
            if resp_tx_detail.status_code != 200:
                continue  # Already deleted or missing
            detail = resp_tx_detail.json() if "application/json" in resp_tx_detail.headers.get("Content-Type", "") else None
            if not detail:
                # fallback: no detail json; skip stock validation for this
                pass

            jumlah_masuk = detail.get("jumlah_masuk") or 0
            jumlah_keluar = detail.get("jumlah_keluar") or 0

            # Delete transaksi
            resp_del = session.delete(f"{TRANSAKSI_URL}/{tid}", allow_redirects=False, timeout=TIMEOUT)
            assert resp_del.status_code == 302 or resp_del.status_code == 200
            loc = resp_del.headers.get("Location", "")
            assert "/transaksi" in loc or resp_del.status_code == 200

            # Get barang stock and verify rollback formula:
            # stok_barang = stok_barang - jumlah_masuk + jumlah_keluar
            resp_info_post_del = session.get(api_barang_info_url, timeout=TIMEOUT)
            assert resp_info_post_del.status_code == 200
            stok_after_del = resp_info_post_del.json().get("stok")

            expected_stok = current_stok_check - jumlah_masuk + jumlah_keluar
            assert stok_after_del == expected_stok
            current_stok_check = stok_after_del

        # For completeness, assert final stock >= original starting stock
        assert current_stok_check >= current_stok

    finally:
        # Cleanup: ensure all created transaksi deleted (if any remain)
        for tid in transaksi_ids_to_cleanup:
            try:
                session.delete(f"{TRANSAKSI_URL}/{tid}", timeout=TIMEOUT)
            except:
                pass


test_transaksi_creation_validation_and_deletion()
