import { test as baseTest, expect, Page } from '@playwright/test';
import { LoginPage, DashboardPage, BarangListPage, TransaksiCreatePage } from './pages';

/**
 * Test user credentials
 * These should match your Laravel seed data
 */
export const TEST_USERS = {
  admin: {
    username: 'admin',
    password: 'admin123',
    name: 'Administrator',
    role: 'admin',
  },
  user: {
    username: 'user',
    password: 'user123',
    name: 'User',
    role: 'user',
  },
};

/**
 * Test fixtures extending Playwright's base test
 * Provides authenticated pages and page objects
 */
interface Fixtures {
  // Page objects
  loginPage: LoginPage;
  dashboardPage: DashboardPage;
  barangListPage: BarangListPage;
  transaksiCreatePage: TransaksiCreatePage;
  
  // Authenticated pages
  adminPage: Page;
  userPage: Page;
}

/**
 * Extended test with fixtures
 */
export const test = baseTest.extend<Fixtures>({
  // Page object fixtures
  loginPage: async ({ page }, use) => {
    await use(new LoginPage(page));
  },
  
  dashboardPage: async ({ page }, use) => {
    await use(new DashboardPage(page));
  },
  
  barangListPage: async ({ page }, use) => {
    await use(new BarangListPage(page));
  },
  
  transaksiCreatePage: async ({ page }, use) => {
    await use(new TransaksiCreatePage(page));
  },
  
  // Authenticated page fixtures
  adminPage: async ({ page }, use) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto();
    await loginPage.login(TEST_USERS.admin.username, TEST_USERS.admin.password);
    await expect(page).toHaveURL(/\/(dashboard)?/);
    await use(page);
  },
  
  userPage: async ({ page }, use) => {
    const loginPage = new LoginPage(page);
    await loginPage.goto();
    await loginPage.login(TEST_USERS.user.username, TEST_USERS.user.password);
    await expect(page).toHaveURL(/\/(dashboard)?/);
    await use(page);
  },
});

export { expect };

/**
 * Helper function to login as specific user
 */
export async function loginAs(page: Page, userType: 'admin' | 'user') {
  const loginPage = new LoginPage(page);
  const user = TEST_USERS[userType];
  
  await loginPage.goto();
  await loginPage.login(user.username, user.password);
  await expect(page).toHaveURL(/\/(dashboard)?/);
}

/**
 * Helper function to logout
 */
export async function logout(page: Page) {
  await page.goto('/logout', { method: 'post' } as any);
  await page.waitForLoadState('networkidle');
}

/**
 * Wait for success notification
 */
export async function waitForSuccess(page: Page, timeout = 5000) {
  const successAlert = page.locator('.alert-success');
  await expect(successAlert).toBeVisible({ timeout });
}

/**
 * Wait for error notification
 */
export async function waitForError(page: Page, timeout = 5000) {
  const errorAlert = page.locator('.alert-danger');
  await expect(errorAlert).toBeVisible({ timeout });
}
