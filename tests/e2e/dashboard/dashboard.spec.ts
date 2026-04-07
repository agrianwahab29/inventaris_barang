import { test, expect, waitForSuccess } from '../fixtures';
import { DashboardPage, BarangListPage, TransaksiListPage, TransaksiCreatePage } from '../pages';

/**
 * Dashboard E2E Tests
 * Tests dashboard functionality: stats, navigation, quick actions, alerts
 */
test.describe('Dashboard Flow', () => {
  
  test.beforeEach(async ({ adminPage: page }) => {
    // Ensure we're on dashboard before each test
    await page.goto('/dashboard');
    await page.waitForLoadState('networkidle');
  });
  
  test('should display dashboard with all key elements', async ({ adminPage: page }) => {
    const dashboardPage = new DashboardPage(page);
    
    // Verify main elements are visible
    await dashboardPage.verifyPageLoaded();
    
    // Verify welcome banner
    await expect(dashboardPage.welcomeBanner).toBeVisible();
    await expect(dashboardPage.welcomeBanner).toContainText('Selamat Datang');
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/dashboard-overview.png', fullPage: true });
    
    // Verify page title
    await expect(page).toHaveTitle(/Dashboard/);
  });
  
  test('should display statistics cards correctly', async ({ adminPage: page }) => {
    const dashboardPage = new DashboardPage(page);
    
    // Get all stat values
    const stats = await dashboardPage.getStats();
    
    // Verify we have stats
    expect(stats.values.length).toBeGreaterThan(0);
    expect(stats.labels.length).toBeGreaterThan(0);
    
    // Verify stat values are numbers (or formatted numbers)
    for (const value of stats.values) {
      expect(value).toBeTruthy();
    }
    
    // Take screenshot of stats section
    await dashboardPage.statCards.first().screenshot({ path: 'artifacts/dashboard-stats.png' });
  });
  
  test('should navigate to Barang page from sidebar', async ({ adminPage: page }) => {
    const dashboardPage = new DashboardPage(page);
    
    // Click on Barang in sidebar
    await dashboardPage.clickSidebarLink('barang');
    
    // Verify navigation
    await expect(page).toHaveURL(/\/barang/);
    
    // Verify Barang page is loaded
    const barangPage = new BarangListPage(page);
    await barangPage.verifyPageLoaded();
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/dashboard-nav-barang.png' });
  });
  
  test('should navigate to Transaksi pages from sidebar', async ({ adminPage: page }) => {
    const dashboardPage = new DashboardPage(page);
    
    // Test navigation to Transaksi Create
    await dashboardPage.clickSidebarLink('transaksiCreate');
    await expect(page).toHaveURL(/transaksi\/create/);
    
    const transaksiCreatePage = new TransaksiCreatePage(page);
    await transaksiCreatePage.verifyPageLoaded();
    
    await page.screenshot({ path: 'artifacts/dashboard-nav-transaksi-create.png' });
    
    // Navigate back and test Transaksi Index
    await dashboardPage.goto();
    await dashboardPage.clickSidebarLink('transaksiIndex');
    await expect(page).toHaveURL(/transaksi(?!\/create)/);
    
    const transaksiListPage = new TransaksiListPage(page);
    await transaksiListPage.verifyPageLoaded();
    
    await page.screenshot({ path: 'artifacts/dashboard-nav-transaksi-index.png' });
  });
  
  test('should navigate to Ruangan page from sidebar', async ({ adminPage: page }) => {
    const dashboardPage = new DashboardPage(page);
    
    await dashboardPage.clickSidebarLink('ruangan');
    await expect(page).toHaveURL(/\/ruangan/);
    
    // Verify page has loaded (check for table or main content)
    await expect(page.locator('.data-card, table, h1')).toBeVisible();
    
    await page.screenshot({ path: 'artifacts/dashboard-nav-ruangan.png' });
  });
  
  test('should work with quick actions', async ({ adminPage: page }) => {
    const dashboardPage = new DashboardPage(page);
    
    // Verify quick actions are visible
    await expect(dashboardPage.quickActions.first()).toBeVisible();
    
    // Click on quick action "Barang Masuk/Keluar"
    await dashboardPage.clickQuickAction('Barang Masuk/Keluar');
    
    // Should navigate to transaksi create
    await expect(page).toHaveURL(/transaksi\/create/);
    
    await page.screenshot({ path: 'artifacts/dashboard-quick-action.png' });
  });
  
  test('should display transaction chart', async ({ adminPage: page }) => {
    const dashboardPage = new DashboardPage(page);
    
    // Scroll to chart section
    await dashboardPage.chart.scrollIntoViewIfNeeded();
    
    // Verify chart is visible
    await expect(dashboardPage.chart).toBeVisible();
    
    // Take screenshot of chart area
    const chartContainer = page.locator('.chart-container').first();
    await chartContainer.screenshot({ path: 'artifacts/dashboard-chart.png' });
  });
  
  test('should display recent transactions list', async ({ adminPage: page }) => {
    const dashboardPage = new DashboardPage(page);
    
    // Scroll to recent transactions section
    await dashboardPage.recentTransactions.scrollIntoViewIfNeeded();
    
    // Verify section header
    await expect(page.locator('text=Transaksi Terakhir')).toBeVisible();
    
    // If there are transactions, verify they display correctly
    const transactionCount = await dashboardPage.recentTransactions.count();
    if (transactionCount > 0) {
      // Verify transaction item structure
      const firstTransaction = dashboardPage.recentTransactions.first();
      await expect(firstTransaction.locator('.transaction-icon')).toBeVisible();
    }
    
    await page.screenshot({ path: 'artifacts/dashboard-recent-transactions.png' });
  });
  
  test('should display stock alerts section', async ({ adminPage: page }) => {
    const dashboardPage = new DashboardPage(page);
    
    // Scroll to stock alerts section
    await dashboardPage.stockAlerts.scrollIntoViewIfNeeded();
    
    // Verify section header
    await expect(page.locator('text=Peringatan Stok')).toBeVisible();
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/dashboard-stock-alerts.png' });
  });
  
  test('should show correct user info in sidebar', async ({ adminPage: page }) => {
    const dashboardPage = new DashboardPage(page);
    
    // Verify user info is displayed
    const userName = page.locator('.user-name');
    const userRole = page.locator('.user-role');
    
    await expect(userName).toBeVisible();
    await expect(userRole).toBeVisible();
    
    // Verify role text
    const roleText = await userRole.textContent();
    expect(roleText?.toLowerCase()).toMatch(/admin|user/);
    
    // Take screenshot
    await page.locator('.user-card').screenshot({ path: 'artifacts/dashboard-user-info.png' });
  });
  
  test('should be responsive on different screen sizes', async ({ adminPage: page }) => {
    const dashboardPage = new DashboardPage(page);
    
    // Test tablet size
    await page.setViewportSize({ width: 768, height: 1024 });
    await page.waitForLoadState('networkidle');
    await dashboardPage.verifyPageLoaded();
    await page.screenshot({ path: 'artifacts/dashboard-tablet.png' });
    
    // Test mobile size
    await page.setViewportSize({ width: 375, height: 667 });
    await page.waitForLoadState('networkidle');
    await expect(dashboardPage.welcomeBanner).toBeVisible();
    await page.screenshot({ path: 'artifacts/dashboard-mobile.png', fullPage: true });
    
    // Reset to desktop
    await page.setViewportSize({ width: 1366, height: 768 });
    await page.waitForLoadState('networkidle');
  });
  
  test('should refresh dashboard data', async ({ adminPage: page }) => {
    const dashboardPage = new DashboardPage(page);
    
    // Verify initial load
    await dashboardPage.verifyPageLoaded();
    
    // Reload the page
    await page.reload();
    await page.waitForLoadState('networkidle');
    
    // Should still display correctly
    await dashboardPage.verifyPageLoaded();
    await expect(page).toHaveURL(/dashboard/);
    
    await page.screenshot({ path: 'artifacts/dashboard-refresh.png' });
  });
});
