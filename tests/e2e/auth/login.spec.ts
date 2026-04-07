import { test, expect, loginAs } from '../fixtures';
import { LoginPage, DashboardPage } from '../pages';
import { TEST_USERS } from '../fixtures';

/**
 * Authentication E2E Tests
 * Tests critical user flows: login, logout, and access control
 */
test.describe('Authentication Flow', () => {
  
  test('should display login page correctly', async ({ page, loginPage }) => {
    // Navigate to login
    await loginPage.goto();
    
    // Verify page elements
    await loginPage.verifyPageLoaded();
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/login-page.png', fullPage: true });
    
    // Verify page title
    await expect(page).toHaveTitle(/Login/);
  });
  
  test('should login successfully as admin', async ({ page, loginPage }) => {
    // Navigate to login
    await loginPage.goto();
    
    // Login with admin credentials
    await loginPage.login(TEST_USERS.admin.username, TEST_USERS.admin.password);
    
    // Verify redirect to dashboard
    await expect(page).toHaveURL(/\/(dashboard)?/);
    
    // Verify dashboard is loaded
    const dashboardPage = new DashboardPage(page);
    await dashboardPage.verifyPageLoaded();
    
    // Verify welcome message
    await dashboardPage.verifyWelcomeMessage(TEST_USERS.admin.name);
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/login-admin-success.png' });
    
    // Verify sidebar is visible
    await expect(dashboardPage.sidebar).toBeVisible();
  });
  
  test('should login successfully as regular user', async ({ page, loginPage }) => {
    // Navigate to login
    await loginPage.goto();
    
    // Login with user credentials
    await loginPage.login(TEST_USERS.user.username, TEST_USERS.user.password);
    
    // Verify redirect to dashboard
    await expect(page).toHaveURL(/\/(dashboard)?/);
    
    // Verify dashboard is loaded
    const dashboardPage = new DashboardPage(page);
    await dashboardPage.verifyPageLoaded();
    
    // Verify welcome message
    await dashboardPage.verifyWelcomeMessage(TEST_USERS.user.name);
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/login-user-success.png' });
  });
  
  test('should show error with invalid credentials', async ({ page, loginPage }) => {
    // Navigate to login
    await loginPage.goto();
    
    // Try to login with wrong credentials
    await loginPage.login('invaliduser', 'wrongpassword');
    
    // Should stay on login page
    await expect(page).toHaveURL(/login/);
    
    // Verify error message
    await loginPage.verifyErrorMessage();
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/login-error.png', fullPage: true });
  });
  
  test('should show error with empty credentials', async ({ page, loginPage }) => {
    // Navigate to login
    await loginPage.goto();
    
    // Try to submit empty form
    await loginPage.submitButton.click();
    await page.waitForLoadState('networkidle');
    
    // Should stay on login page (browser validation may prevent submission)
    await expect(page.url()).toContain('login');
  });
  
  test('should redirect to login when accessing protected page without authentication', async ({ page }) => {
    // Try to access dashboard without login
    await page.goto('/dashboard');
    await page.waitForLoadState('networkidle');
    
    // Should be redirected to login
    await expect(page).toHaveURL(/login/);
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/protected-redirect.png' });
  });
  
  test('should logout successfully', async ({ adminPage: page }) => {
    // Create dashboard page instance
    const dashboardPage = new DashboardPage(page);
    
    // Verify we're logged in (dashboard is visible)
    await dashboardPage.verifyPageLoaded();
    
    // Click logout button (in sidebar footer)
    const logoutButton = page.locator('button[title="Logout"], button.logout-btn');
    await logoutButton.click();
    
    // Wait for navigation
    await page.waitForLoadState('networkidle');
    
    // Should be redirected to login
    await expect(page).toHaveURL(/login/);
    
    // Verify login page is shown
    const loginPage = new LoginPage(page);
    await loginPage.verifyPageLoaded();
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/logout-success.png', fullPage: true });
  });
  
  test('should not access admin routes as regular user', async ({ page }) => {
    // Login as regular user
    await loginAs(page, 'user');
    
    // Try to access admin-only routes
    await page.goto('/users');
    await page.waitForLoadState('networkidle');
    
    // Should be redirected (403 or back to dashboard)
    const currentUrl = page.url();
    expect(currentUrl).not.toContain('/users');
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/user-admin-restriction.png' });
  });
  
  test('should maintain session after page refresh', async ({ adminPage: page }) => {
    const dashboardPage = new DashboardPage(page);
    
    // Verify initial load
    await dashboardPage.verifyPageLoaded();
    
    // Refresh the page
    await page.reload();
    await page.waitForLoadState('networkidle');
    
    // Should still be logged in
    await dashboardPage.verifyPageLoaded();
    await expect(page).toHaveURL(/dashboard/);
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/session-persist-after-refresh.png' });
  });
  
  test('should have proper form validation on login', async ({ page, loginPage }) => {
    await loginPage.goto();
    
    // Test username is required
    await loginPage.passwordInput.fill('somepassword');
    await loginPage.submitButton.click();
    
    // Page should show validation feedback
    const usernameInput = loginPage.usernameInput;
    const isRequired = await usernameInput.evaluate((el: HTMLInputElement) => el.required);
    expect(isRequired).toBe(true);
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/login-validation.png', fullPage: true });
  });
});
