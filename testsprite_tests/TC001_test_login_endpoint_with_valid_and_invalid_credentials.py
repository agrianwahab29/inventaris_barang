import requests
import re

def get_csrf_token(content):
    # Parse csrf_token from meta tag or hidden input
    meta_match = re.search(r'<meta\s+name=["\']csrf-token["\']\s+content=["\']([^"\']+)["\']', content)
    if meta_match:
        return meta_match.group(1)
    input_match = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', content)
    if input_match:
        return input_match.group(1)
    return None

def test_login_endpoint_with_valid_and_invalid_credentials():
    base_url = "http://localhost:8000"
    login_url = f"{base_url}/login"
    dashboard_url = f"{base_url}/dashboard"

    timeout = 30

    # Start session for valid login
    session = requests.Session()

    try:
        # Initial GET to /login to fetch CSRF token
        get_resp = session.get(login_url, timeout=timeout)
        assert get_resp.status_code == 200, f"GET /login expected 200, got {get_resp.status_code}"
        csrf_token = get_csrf_token(get_resp.text)
        assert csrf_token is not None, "CSRF token not found in login form"

        # Test POST /login with valid credentials including CSRF token
        valid_payload = {
            '_token': csrf_token,
            'username': 'admin',
            'password': 'admin123'
        }

        response = session.post(login_url, data=valid_payload, allow_redirects=False, timeout=timeout)

        # Expect status code 302 redirect to /dashboard
        assert response.status_code == 302, f"Expected 302 redirect, got {response.status_code}"
        location = response.headers.get('Location', '')
        assert location.endswith('/dashboard'), f"Expected redirect to /dashboard, got redirect to {location}"

        # Validate presence of session cookie (Laravel uses 'laravel_session' cookie by default)
        cookies = session.cookies.get_dict()
        assert any(name.startswith('laravel_session') or name == 'laravel_session' for name in cookies), "Session cookie missing after login"

        # Use session cookie to access /dashboard to validate authentication
        dashboard_resp = session.get(dashboard_url, timeout=timeout)
        assert dashboard_resp.status_code == 200, f"Authenticated GET /dashboard expected 200, got {dashboard_resp.status_code}"
        # Optionally validate presence of user profile and role badge keywords in dashboard page content
        content = dashboard_resp.text.lower()
        assert ('admin' in content or 'role' in content), "Dashboard content does not contain expected user profile or role badge"
    finally:
        session.close()

    # Test POST /login with invalid credentials, new session
    session_invalid = requests.Session()
    try:
        # GET /login to fetch CSRF token
        get_resp_invalid = session_invalid.get(login_url, timeout=timeout)
        assert get_resp_invalid.status_code == 200, f"GET /login expected 200, got {get_resp_invalid.status_code}"
        csrf_token_invalid = get_csrf_token(get_resp_invalid.text)
        assert csrf_token_invalid is not None, "CSRF token not found in login form"

        invalid_payload = {
            '_token': csrf_token_invalid,
            'username': 'admin',
            'password': 'wrongpassword'
        }

        # Perform login with invalid credentials, not following redirects
        response_invalid = session_invalid.post(login_url, data=invalid_payload, allow_redirects=False, timeout=timeout)

        # Expect status code 302 redirect back to /login
        assert response_invalid.status_code == 302, f"Expected 302 redirect for invalid credentials, got {response_invalid.status_code}"
        location_invalid = response_invalid.headers.get('Location', '')
        assert location_invalid.endswith('/login'), f"Expected redirect back to /login, got redirect to {location_invalid}"

        # To verify error message "Username atau password salah", follow redirect
        follow_resp = session_invalid.get(f"{base_url}{location_invalid}", timeout=timeout)
        assert follow_resp.status_code == 200, f"Expected 200 OK on redirected login page, got {follow_resp.status_code}"
        expected_error_msg = "Username atau password salah"
        assert expected_error_msg.lower() in follow_resp.text.lower(), f"Expected error message '{expected_error_msg}' not found in login page"
    finally:
        session_invalid.close()


test_login_endpoint_with_valid_and_invalid_credentials()