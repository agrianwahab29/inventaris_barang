import requests

BASE_URL = "http://localhost:8000"
USERNAME = "admin"
PASSWORD = "admin123"
TIMEOUT = 30

def test_dashboard_access_with_and_without_authentication():
    session = requests.Session()

    # 1. Attempt GET /dashboard without authentication - expect redirect to /login
    url_dashboard = f"{BASE_URL}/dashboard"
    response = session.get(url_dashboard, allow_redirects=False, timeout=TIMEOUT)
    assert response.status_code == 302, f"Expected 302 redirect on unauthenticated /dashboard, got {response.status_code}"
    location = response.headers.get("Location", "")
    assert location.endswith("/login"), f"Expected redirect to /login but got redirect to {location}"

    # 2. Attempt GET / (root) without authentication - expect redirect to /login
    url_root = f"{BASE_URL}/"
    response = session.get(url_root, allow_redirects=False, timeout=TIMEOUT)
    assert response.status_code == 302, f"Expected 302 redirect on unauthenticated /, got {response.status_code}"
    location = response.headers.get("Location", "")
    assert location.endswith("/login"), f"Expected redirect to /login but got redirect to {location}"

    # 3. Authenticate by POST /login with valid credentials, follow redirects manually to preserve session
    login_url = f"{BASE_URL}/login"
    login_data = {
        "username": USERNAME,
        "password": PASSWORD
    }
    # Laravel expects POST login with form data. According to PRD, POST /login with valid credentials -> 302 redirect to /dashboard and session cookie.
    login_response = session.post(login_url, data=login_data, allow_redirects=False, timeout=TIMEOUT)
    assert login_response.status_code == 302, f"Login POST expected 302 redirect, got {login_response.status_code}"
    login_redirect = login_response.headers.get("Location", "")
    assert login_redirect.endswith("/dashboard"), f"Expected redirect to /dashboard after login, got redirect to {login_redirect}"

    # Follow redirect to /dashboard to establish authenticated session
    dashboard_response = session.get(f"{BASE_URL}{login_redirect}", timeout=TIMEOUT)
    assert dashboard_response.status_code == 200, f"Expected 200 OK at /dashboard after login, got {dashboard_response.status_code}"
    dashboard_content = dashboard_response.text.lower()

    # Check for inventory statistics keywords or elements
    stat_keywords = ["total barang", "stok rendah", "transaksi hari ini", "transaksi bulan ini"]
    quick_actions = ["/transaksi/create?tipe=masuk", "/transaksi/create?tipe=keluar", "/barang?filter=stok_rendah"]
    for kw in stat_keywords:
        assert kw in dashboard_content, f"Dashboard missing expected inventory statistic keyword '{kw}'"
    for qa in quick_actions:
        assert qa in dashboard_content, f"Dashboard missing expected quick action link '{qa}'"

    # 4. Authenticated GET / (root) expect 200 with same dashboard info
    root_response = session.get(url_root, timeout=TIMEOUT)
    assert root_response.status_code == 200, f"Authenticated GET / expected 200 OK, got {root_response.status_code}"
    root_content = root_response.text.lower()
    for kw in stat_keywords:
        assert kw in root_content, f"Root / missing expected inventory statistic keyword '{kw}'"
    for qa in quick_actions:
        assert qa in root_content, f"Root / missing expected quick action link '{qa}'"

test_dashboard_access_with_and_without_authentication()