import requests
import re

BASE_URL = "http://localhost:8000"
TIMEOUT = 30
HEADERS = {"Accept": "application/json"}


def get_csrf_token(session, url):
    r = session.get(url, timeout=TIMEOUT)
    r.raise_for_status()
    match = re.search(r'name="_token" value="([^"]+)"', r.text)
    assert match, f"CSRF token not found in {url}"
    return match.group(1)


def login_as_admin(session):
    # GET /login to get CSRF token
    csrf_token = get_csrf_token(session, f"{BASE_URL}/login")
    login_payload = {
        '_token': csrf_token,
        'username': 'admin',
        'password': 'admin123'
    }
    r = session.post(f"{BASE_URL}/login", data=login_payload, timeout=TIMEOUT, allow_redirects=False)
    # Expect 302 redirect to /dashboard
    assert r.status_code == 302 and '/dashboard' in r.headers.get('Location', ''), f"Login failed with status {r.status_code}"


def test_user_management_crud_and_self_delete_prevention():
    session = requests.Session()
    session.headers.update(HEADERS)
    created_user_id = None
    try:
        # Login first
        login_as_admin(session)

        # Get CSRF token for user creation
        csrf_token = get_csrf_token(session, f"{BASE_URL}/users/create")

        # 1. Create new user with unique username and email
        new_user_payload = {
            '_token': csrf_token,
            'name': 'Test User',
            'username': 'testuser_unique_001',
            'email': 'testuser_unique_001@example.com',
            'password': 'password123',
            'role': 'pengguna'
        }
        r = session.post(f"{BASE_URL}/users", data=new_user_payload, timeout=TIMEOUT, allow_redirects=False)
        assert r.status_code == 302, f"Expected 302 redirect, got {r.status_code}"

        # Extract new user ID by fetching user edit page
        r_edit = session.get(f"{BASE_URL}/users/testuser_unique_001/edit", timeout=TIMEOUT)
        assert r_edit.status_code == 200, "Newly created user edit page not found to get user id"
        created_user_id = "testuser_unique_001"

        # Get CSRF token for next requests
        csrf_token_update = get_csrf_token(session, f"{BASE_URL}/users/{created_user_id}/edit")

        # 2. Try creating another user with same username, expect 422 validation error
        duplicate_username_payload = {
            '_token': csrf_token,
            'name': 'Another User',
            'username': 'testuser_unique_001',
            'email': 'anotheremail@example.com',
            'password': 'password321',
            'role': 'pengguna'
        }
        r_dup_username = session.post(f"{BASE_URL}/users", data=duplicate_username_payload, timeout=TIMEOUT)
        assert r_dup_username.status_code == 422, f"Expected 422 for duplicate username, got {r_dup_username.status_code}"
        assert "Username sudah digunakan" in r_dup_username.text or "Email/Username sudah digunakan" in r_dup_username.text

        # 3. Try creating user with existing email, expect 422 validation error
        duplicate_email_payload = {
            '_token': csrf_token,
            'name': 'Another User',
            'username': 'uniqueusername002',
            'email': 'testuser_unique_001@example.com',
            'password': 'password321',
            'role': 'pengguna'
        }
        r_dup_email = session.post(f"{BASE_URL}/users", data=duplicate_email_payload, timeout=TIMEOUT)
        assert r_dup_email.status_code == 422, f"Expected 422 for duplicate email, got {r_dup_email.status_code}"
        assert "Email sudah digunakan" in r_dup_email.text or "Email/Username sudah digunakan" in r_dup_email.text

        # 4. Update the created user without changing password (password empty)
        update_payload = {
            '_token': csrf_token_update,
            'name': 'Test User Updated',
            'username': 'testuser_unique_001',
            'email': 'testuser_unique_001@example.com',
            'password': '',  # Empty should preserve old password
            'role': 'pengguna'
        }
        r_update = session.put(f"{BASE_URL}/users/{created_user_id}", data=update_payload, timeout=TIMEOUT, allow_redirects=False)
        assert r_update.status_code == 302, f"Expected 302 redirect on update, got {r_update.status_code}"

        # Get CSRF token for delete request
        csrf_token_delete = get_csrf_token(session, f"{BASE_URL}/users/{created_user_id}/edit")

        # 5. Attempt to delete own admin account (username=admin), expect 400 or 422 error with self-delete prevention
        admin_user_id = "admin"
        headers_delete = HEADERS.copy()
        headers_delete["X-CSRF-TOKEN"] = csrf_token_delete
        r_delete_self = session.delete(f"{BASE_URL}/users/{admin_user_id}", headers=headers_delete, timeout=TIMEOUT)
        assert r_delete_self.status_code in (400, 422), f"Expected 400/422 on self-delete, got {r_delete_self.status_code}"
        assert "Anda tidak dapat menghapus akun Anda sendiri" in r_delete_self.text

    finally:
        # Cleanup: delete created user if exists and not admin
        if created_user_id and created_user_id != "admin":
            csrf_token_cleanup = get_csrf_token(session, f"{BASE_URL}/users/{created_user_id}/edit")
            headers_cleanup = HEADERS.copy()
            headers_cleanup["X-CSRF-TOKEN"] = csrf_token_cleanup
            session.delete(f"{BASE_URL}/users/{created_user_id}", headers=headers_cleanup, timeout=TIMEOUT)


test_user_management_crud_and_self_delete_prevention()
