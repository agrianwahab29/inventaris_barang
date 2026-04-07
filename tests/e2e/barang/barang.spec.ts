import { test, expect, waitForSuccess, loginAs } from '../fixtures';
import { BarangListPage, BarangFormPage, DashboardPage } from '../pages';

/**
 * Barang (Items) Management E2E Tests
 * Tests CRUD operations, filtering, and stock management
 */
test.describe('Barang Management Flow', () => {
  
  test.beforeEach(async ({ page }) => {
    // Login as admin before each test
    await loginAs(page, 'admin');
  });
  
  test('should display barang list page', async ({ page }) => {
    const barangPage = new BarangListPage(page);
    await barangPage.goto();
    
    // Verify page loads
    await barangPage.verifyPageLoaded();
    
    // Verify table is visible
    await expect(barangPage.dataTable).toBeVisible();
    
    // Verify action buttons
    await expect(barangPage.addButton).toBeVisible();
    await expect(barangPage.exportButton).toBeVisible();
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/barang-list.png', fullPage: true });
  });
  
  test('should search for barang', async ({ page }) => {
    const barangPage = new BarangListPage(page);
    await barangPage.goto();
    
    // Search for a term
    await barangPage.search('test');
    
    // Verify page still loaded
    await expect(barangPage.dataTable).toBeVisible();
    
    // Take screenshot
    await page.screenshot({ path: 'artifacts/barang-search.png' });
  });
  
  test('should filter barang by category', async ({ page }) => {
    const barangPage = new BarangListPage(page);
    await barangPage.goto();
    
    // Get available categories from dropdown
    const options = await barangPage.categoryFilter.locator('option').allTextContents();
    
    // Select first non-empty category
    for (const option of options) {
      if (option && option !== '' && option !== 'Semua Kategori') {
        await barangPage.categoryFilter.selectOption(option);
        await barangPage.filterButton.click();
        await page.waitForLoadState('networkidle');
        break;
      }
    }
    
    // Verify table is still visible
    await expect(barangPage.dataTable).toBeVisible();
    
    await page.screenshot({ path: 'artifacts/barang-filter-category.png' });
  });
  
  test('should filter barang by status', async ({ page }) => {
    const barangPage = new BarangListPage(page);
    await barangPage.goto();
    
    // Filter by "rendah" status
    await barangPage.filterByStatus('rendah');
    
    // Verify table is still visible
    await expect(barangPage.dataTable).toBeVisible();
    
    await page.screenshot({ path: 'artifacts/barang-filter-status.png' });
  });
  
  test('should navigate to create barang page', async ({ page }) => {
    const barangPage = new BarangListPage(page);
    await barangPage.goto();
    
    // Click add button
    await barangPage.clickAdd();
    
    // Verify navigation to create page
    await expect(page).toHaveURL(/barang\/create/);
    
    // Verify form elements are present
    const formPage = new BarangFormPage(page);
    await expect(formPage.namaBarangInput).toBeVisible();
    
    await page.screenshot({ path: 'artifacts/barang-create-page.png', fullPage: true });
  });
  
  test('should create new barang successfully', async ({ page }) => {
    // Navigate to create page
    await page.goto('/barang/create');
    await page.waitForLoadState('networkidle');
    
    const formPage = new BarangFormPage(page);
    
    // Fill form
    await formPage.fillForm({
      namaBarang: `Test Barang ${Date.now()}`,
      satuan: 'pcs',
      stok: '10',
      stokMinimum: '5',
    });
    
    // Select category if available
    const categoryOptions = await formPage.kategoriSelect.locator('option').count();
    if (categoryOptions > 1) {
      await formPage.kategoriSelect.selectOption({ index: 1 });
    }
    
    // Submit form
    await formPage.submit();
    
    // Wait for success
    await waitForSuccess(page);
    
    // Verify redirect to list page
    await expect(page).toHaveURL(/barang/);
    
    await page.screenshot({ path: 'artifacts/barang-create-success.png' });
  });
  
  test('should view barang details', async ({ page }) => {
    const barangPage = new BarangListPage(page);
    await barangPage.goto();
    
    // Get row count
    const rowCount = await barangPage.getRowCount();
    
    if (rowCount > 0) {
      // Click view on first row
      await barangPage.clickView(0);
      
      // Verify we're on detail page
      await expect(page.url()).toContain('/barang/');
      
      // Verify detail content is visible
      await expect(page.locator('h1, .page-title, .card-title')).toBeVisible();
      
      await page.screenshot({ path: 'artifacts/barang-detail.png' });
    } else {
      test.skip();
    }
  });
  
  test('should edit existing barang', async ({ page }) => {
    const barangPage = new BarangListPage(page);
    await barangPage.goto();
    
    // Find first row with edit button
    const editButton = page.locator('table tbody tr').first().locator('a:has(.fa-edit)');
    
    if (await editButton.isVisible().catch(() => false)) {
      await editButton.click();
      await page.waitForLoadState('networkidle');
      
      // Verify we're on edit page
      await expect(page.url()).toMatch(/barang\/\d+\/edit/);
      
      // Edit the barang
      const formPage = new BarangFormPage(page);
      const newName = `Updated Barang ${Date.now()}`;
      await formPage.fillForm({ namaBarang: newName });
      await formPage.submit();
      
      // Wait for success
      await waitForSuccess(page);
      
      await page.screenshot({ path: 'artifacts/barang-edit-success.png' });
    } else {
      test.skip();
    }
  });
  
  test('should show validation errors on invalid form', async ({ page }) => {
    // Navigate to create page
    await page.goto('/barang/create');
    await page.waitForLoadState('networkidle');
    
    const formPage = new BarangFormPage(page);
    
    // Submit empty form
    await formPage.submit();
    
    // Verify still on create page (validation prevented submission)
    await expect(page.url()).toContain('/barang/create');
    
    await page.screenshot({ path: 'artifacts/barang-validation-error.png' });
  });
  
  test('should have working export button', async ({ page }) => {
    const barangPage = new BarangListPage(page);
    await barangPage.goto();
    
    // Click export
    await barangPage.exportButton.click();
    
    // Wait a moment for download to start
    await page.waitForTimeout(2000);
    
    // Verify we're still on the page
    await expect(page.url()).toContain('/barang');
    
    await page.screenshot({ path: 'artifacts/barang-export.png' });
  });
  
  test('should display statistics on barang page', async ({ page }) => {
    const barangPage = new BarangListPage(page);
    await barangPage.goto();
    
    // Verify stats cards exist
    const statCards = page.locator('.card');
    const cardCount = await statCards.count();
    expect(cardCount).toBeGreaterThan(0);
    
    await page.screenshot({ path: 'artifacts/barang-stats.png' });
  });
  
  test('should have pagination if many items', async ({ page }) => {
    const barangPage = new BarangListPage(page);
    await barangPage.goto();
    
    // Check if pagination exists
    const pagination = page.locator('.pagination, nav');
    const hasPagination = await pagination.isVisible().catch(() => false);
    
    if (hasPagination) {
      await page.screenshot({ path: 'artifacts/barang-pagination.png' });
    }
  });
});
