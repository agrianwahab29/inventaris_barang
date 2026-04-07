import requests
from requests.auth import HTTPBasicAuth

BASE_URL = "http://localhost:8000"
LOGIN_URL = f"{BASE_URL}/login"
SURAT_TANDA_TERIMA_URL = f"{BASE_URL}/surat-tanda-terima"
SURAT_TANDA_TERIMA_GENERATE_URL = f"{SURAT_TANDA_TERIMA_URL}/generate"

TIMEOUT = 30


def test_surat_tanda_terima_generation_and_validation():
    session = requests.Session()
    try:
        # Step 1: Access GET /login to get login form (no auth required)
        resp = session.get(LOGIN_URL, timeout=TIMEOUT)
        assert resp.status_code == 200
        assert "login" in resp.text.lower()

        # Step 2: POST /login with valid credentials to get authenticated session cookie
        login_resp = session.post(
            LOGIN_URL,
            data={"username": "admin", "password": "admin123"},
            timeout=TIMEOUT,
            allow_redirects=False,
        )
        # Should redirect to /dashboard on success with 302
        assert login_resp.status_code == 302
        assert "Location" in login_resp.headers
        assert login_resp.headers["Location"].startswith("/dashboard")

        # Step 3: Access GET /surat-tanda-terima with authenticated session -> form page
        form_resp = session.get(SURAT_TANDA_TERIMA_URL, timeout=TIMEOUT)
        assert form_resp.status_code == 200
        # Expect form content with fields like nomor_surat, tanggal, dari, kepada, barang_list
        form_text = form_resp.text.lower()
        assert "nomor_surat" in form_text
        assert "tanggal" in form_text
        assert "dari" in form_text
        assert "kepada" in form_text
        assert "barang_list" in form_text

        # Step 4: Attempt GET /surat-tanda-terima/generate with missing required params -> validation error 422
        missing_params_resp = session.get(SURAT_TANDA_TERIMA_GENERATE_URL, timeout=TIMEOUT)
        assert missing_params_resp.status_code == 422
        # Expect error message mentioning missing required params
        assert "nomor_surat" in missing_params_resp.text.lower() or "required" in missing_params_resp.text.lower()

        # Step 5: Perform GET /surat-tanda-terima/generate with all required parameters
        params = {
            "nomor_surat": "STT-2026-001",
            "tanggal": "2026-04-01",
            "dari": "Admin Kantor",
            "kepada": "Bagian Keuangan",
            "barang_list": "Printer, Laptop",
        }
        generate_resp = session.get(SURAT_TANDA_TERIMA_GENERATE_URL, params=params, timeout=TIMEOUT)
        assert generate_resp.status_code == 200
        # The content should be a binary DOCX file
        content_disp = generate_resp.headers.get("Content-Disposition", "")
        assert content_disp.startswith("attachment")
        assert "Tanda_Terima_" in content_disp
        # Check DOCX magic number (PK zip archive)
        assert generate_resp.content[:2] == b'PK'

    finally:
        session.close()


test_surat_tanda_terima_generation_and_validation()