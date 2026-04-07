import { test, expect, waitForSuccess, loginAs } from '../fixtures';
import { RuanganPage, DashboardPage } from '../pages';

/**
 * Ruangan (Rooms) E2E Tests
 * Tests room management and navigation
 */
test.describe('Ruangan Flow', () => {
  
  test.beforeEach(async ({ page }) => {
    // Login as admin before each test
    await loginAs(page, 'admin');
  });
  
  test('should display ruangan list page', async ({ page }) => {
    const ruanganPage = new RuanganPage(page);
    await ruanganPage.goto();
    
    // Verify page loads
    await ruanganPage.verifyPageLoaded();
    
    // Verify table is visible
    await expect(ruanganPage.dataTable).toBeVisible();
    
    await page.screenshot({ path: 'artifacts/ruangan-list.png', fullPage: true });
  });
  
  test('should navigate to ruangan from sidebar', async ({ page }) => {
    const dashboardPage = new DashboardPage(page);
    await dashboardPage.goto();
    
    // Click sidebar link
    await dashboardPage.clickSidebarLink('ruangan');
    
    // Verify navigation
    await expect(page).toHaveURL(/\/ruangan/);
    
    // Verify page loaded
    const ruanganPage = new RuanganPage(page);
    await ruanganPage.verifyPageLoaded();
    
    await page.screenshot({ path: 'artifacts/ruangan-nav-from-sidebar.png' });
  });
  
  test('should view ruangan details', async ({ page }) => {
    const ruanganPage = new RuanganPage(page);
    await ruanganPage.goto();
    
    // Find view button
    const viewButton = page.locator('table tbody tr').first().locator('a:has(.fa-eye)');
    
    if (await viewButton.isVisible().catch(() => false)) {
      await viewButton.click();
      await page.waitForLoadState('networkidle');
      
      // Verify detail page
      await expect(page.url()).toMatch(/ruangan\/\d+/);
      await expect(page.locator('h1, .page-title')).toBeVisible();
      
      await page.screenshot({ path: 'artifacts/ruangan-detail.png' });
    }
  });
  
  test('should create new ruangan (admin only)', async ({ page }) => {
    // Navigate to create page
    await page.goto('/ruangan/create');
    await page.waitForLoadState('networkidle');
    
    // Check if we can access create page (admin should be able to)
    const currentUrl = page.url();
    if (currentUrl.includes('/ruangan/create')) {
      // Fill form
      const namaRuangan = page.locator('input[name="nama_ruangan"], #nama_ruangan');
      const keterangan = page.locator('textarea[name="keterangan"], #keterangan');
      const saveButton = page.locator('button[type="submit"]:has-text("Simpan")');
      
      await namaRuangan.fill(`Test Ruangan ${Date.now()}`);
      await keterangan.fill('Ruangan untuk testing');
      await saveButton.click();
      
      await page.waitForLoadState('networkidle');
      
      await page.screenshot({ path: 'artifacts/ruangan-create-success.png' });
    }
  });
  
  test('should display ruangan table with columns', async ({ page }) => {
    await page.goto('/ruangan');
    await page.waitForLoadState('networkidle');
    
    // Verify table headers
    const headers = await page.locator('table thead th').allTextContents();
    expect(headers.length).toBeGreaterThan(0);
    
    // Check for expected columns
    const headerText = headers.join(' ').toLowerCase();
    expect(headerText).toContain('ruangan');
    
    await page.screenshot({ path: 'artifacts/ruangan-table-headers.png' });
  });
});
