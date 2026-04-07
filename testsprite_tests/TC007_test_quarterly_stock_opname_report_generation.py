import requests

BASE_URL = "http://localhost:8000"
LOGIN_URL = f"{BASE_URL}/login"
QUARTERLY_STOCK_URL = f"{BASE_URL}/quarterly-stock"
QUARTERLY_STOCK_EXPORT_URL = f"{BASE_URL}/quarterly-stock/export"
TIMEOUT = 30

def test_quarterly_stock_opname_report_generation():
    session = requests.Session()
    # Step 1: Authenticate user with valid credentials (simulate POST /login)
    login_payload = {
        "username": "admin",
        "password": "admin123"
    }
    # First, get login form (GET /login)
    login_get_resp = session.get(LOGIN_URL, timeout=TIMEOUT)
    assert login_get_resp.status_code == 200
    
    # POST /login to authenticate
    login_resp = session.post(LOGIN_URL, data=login_payload, allow_redirects=False, timeout=TIMEOUT)
    # Should redirect to /dashboard with 302
    assert login_resp.status_code == 302
    location_header = None
    for key in login_resp.headers.keys():
        if key.lower() == 'location':
            location_header = login_resp.headers[key]
            break
    assert location_header is not None and "/dashboard" in location_header
    # Session cookie must be set for later use
    assert len(session.cookies) > 0
    
    # Step 2: GET /quarterly-stock with authenticated session -> 200 with form and data preview
    get_qs_resp = session.get(QUARTERLY_STOCK_URL, timeout=TIMEOUT)
    assert get_qs_resp.status_code == 200
    # Content should contain form elements related to quarter and year (text checks)
    content = get_qs_resp.text
    assert "quarter" in content.lower() or "triwulan" in content.lower()
    assert "year" in content.lower() or "tahun" in content.lower()
    # Quick check for data preview presence (likely a table or div with stock info)
    assert ("preview" in content.lower() or "data" in content.lower() or "stock" in content.lower())
    
    # Step 3: POST /quarterly-stock/export with valid quarter and year -> 200 with DOCX file
    valid_export_payload = {
        "quarter": "Q1",
        "year": 2026
    }
    post_export_resp = session.post(QUARTERLY_STOCK_EXPORT_URL, data=valid_export_payload, timeout=TIMEOUT)
    assert post_export_resp.status_code == 200
    # Content-Type should be docx or application/vnd.openxmlformats-officedocument.wordprocessingml.document
    content_type = post_export_resp.headers.get("Content-Type", "").lower()
    assert content_type in ("application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/msword")
    # Content-Disposition header should have correct filename
    content_disp = post_export_resp.headers.get("Content-Disposition", "")
    assert content_disp != ""
    assert "Stock_Opname_Q1__2026.docx" in content_disp or "Stock_Opname_Q1_2026.docx" in content_disp.replace('"','')
    # Response content should be binary (non-empty)
    assert len(post_export_resp.content) > 100  # minimal size
    
    # Step 4: POST /quarterly-stock/export with missing quarter -> expect 422 validation error
    missing_quarter_payload = {
        "year": 2026
    }
    post_missing_quarter_resp = session.post(QUARTERLY_STOCK_EXPORT_URL, data=missing_quarter_payload, timeout=TIMEOUT)
    assert post_missing_quarter_resp.status_code == 422
    
    # Step 5: POST /quarterly-stock/export with missing year -> expect 422 validation error
    missing_year_payload = {
        "quarter": "Q1"
    }
    post_missing_year_resp = session.post(QUARTERLY_STOCK_EXPORT_URL, data=missing_year_payload, timeout=TIMEOUT)
    assert post_missing_year_resp.status_code == 422

test_quarterly_stock_opname_report_generation()
