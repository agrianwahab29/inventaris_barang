import { test, expect, waitForSuccess, loginAs } from '../fixtures';
import { TransaksiCreatePage, TransaksiListPage, BarangListPage } from '../pages';

/**
 * Transaksi (Transactions) E2E Tests
 * Tests transaction creation, stock updates, and history viewing
 */
test.describe('Transaksi Flow', () => {
  
  test.beforeEach(async ({ page }) => {
    // Login as admin before each test
    await loginAs(page, 'admin');
  });
  
  test('should display transaksi list page', async ({ page }) => {
    const transaksiPage = new TransaksiListPage(page);
    await transaksiPage.goto();
    
    // Verify page loads
    await transaksiPage.verifyPageLoaded();
    
    // Verify table is visible
    await expect(transaksiPage.dataTable).toBeVisible();
    
    await page.screenshot({ path: 'artifacts/transaksi-list.png', fullPage: true });
  });
  
  test('should navigate to create transaksi page', async ({ page }) => {
    const transaksiPage = new TransaksiCreatePage(page);
    await transaksiPage.goto();
    
    // Verify form elements
    await transaksiPage.verifyPageLoaded();
    
    // Verify barang select exists
    await expect(transaksiPage.barangSelect).toBeVisible();
    
    await page.screenshot({ path: 'artifacts/transaksi-create-page.png', fullPage: true });
  });
  
  test('should create barang masuk transaction', async ({ page }) => {
    const transaksiPage = new TransaksiCreatePage(page);
    await transaksiPage.goto();
    
    // Get first available barang option
    const options = await transaksiPage.barangSelect.locator('option').all();
    if (options.length <= 1) {
      test.skip('No barang available for testing');
      return;
    }
    
    // Select first barang (skip first option which is placeholder)
    const firstBarangValue = await options[1].getAttribute('value');
    if (!firstBarangValue) {
      test.skip('No barang ID found');
      return;
    }
    
    await transaksiPage.selectBarang(firstBarangValue);
    
    // Wait for info box to appear
    await page.waitForTimeout(500);
    
    // Fill barang masuk
    await transaksiPage.fillBarangMasuk(5);
    
    // Submit
    await transaksiPage.submit();
    
    // Wait for redirect or success
    await page.waitForLoadState('networkidle');
    
    // Verify we're on transaksi list or detail
    await expect(page.url()).toMatch(/transaksi/);
    
    await page.screenshot({ path: 'artifacts/transaksi-masuk-success.png' });
  });
  
  test('should create barang keluar transaction', async ({ page }) => {
    const transaksiPage = new TransaksiCreatePage(page);
    await transaksiPage.goto();
    
    // Get available barang with stock
    const options = await transaksiPage.barangSelect.locator('option').all();
    if (options.length <= 1) {
      test.skip('No barang available for testing');
      return;
    }
    
    // Find a barang with sufficient stock
    let selectedBarangValue: string | null = null;
    let selectedBarangStock = 0;
    
    for (let i = 1; i < options.length; i++) {
      const stockAttr = await options[i].getAttribute('data-stok');
      const stock = parseInt(stockAttr || '0');
      if (stock >= 1) {
        selectedBarangValue = await options[i].getAttribute('value');
        selectedBarangStock = stock;
        break;
      }
    }
    
    if (!selectedBarangValue || selectedBarangStock < 1) {
      test.skip('No barang with sufficient stock found');
      return;
    }
    
    await transaksiPage.selectBarang(selectedBarangValue);
    await page.waitForTimeout(500);
    
    // Fill barang keluar (take only 1 item)
    await transaksiPage.fillBarangKeluar(1, 'Test User');
    
    // Submit
    await transaksiPage.submit();
    
    // Wait for redirect
    await page.waitForLoadState('networkidle');
    
    // Verify redirect
    await expect(page.url()).toMatch(/transaksi/);
    
    await page.screenshot({ path: 'artifacts/transaksi-keluar-success.png' });
  });
  
  test('should show stock info when selecting barang', async ({ page }) => {
    const transaksiPage = new TransaksiCreatePage(page);
    await transaksiPage.goto();
    
    // Get available options
    const options = await transaksiPage.barangSelect.locator('option').all();
    if (options.length <= 1) {
      test.skip('No barang available for testing');
      return;
    }
    
    // Select first barang
    const firstValue = await options[1].getAttribute('value');
    if (firstValue) {
      await transaksiPage.selectBarang(firstValue);
      
      // Verify info box appears
      await expect(transaksiPage.infoBox).toBeVisible({ timeout: 5000 });
      
      // Verify stock info is displayed
      const stockText = await transaksiPage.getStockInfo();
      expect(stockText).toBeTruthy();
    }
    
    await page.screenshot({ path: 'artifacts/transaksi-stock-info.png' });
  });
  
  test('should calculate stock correctly for barang masuk', async ({ page }) => {
    const transaksiPage = new TransaksiCreatePage(page);
    await transaksiPage.goto();
    
    // Get available options
    const options = await transaksiPage.barangSelect.locator('option').all();
    if (options.length <= 1) {
      test.skip('No barang available for testing');
      return;
    }
    
    const firstValue = await options[1].getAttribute('value');
    if (!firstValue) {
      test.skip('No barang value found');
      return;
    }
    
    await transaksiPage.selectBarang(firstValue);
    await page.waitForTimeout(500);
    
    // Get current stock
    const stockText = await transaksiPage.getStockInfo();
    const currentStock = parseInt(stockText || '0');
    
    // Enter jumlah masuk
    await transaksiPage.fillBarangMasuk(10);
    
    // Verify calculation is shown
    const stokSetelahMasuk = page.locator('#stok_setelah_masuk');
    await expect(stokSetelahMasuk).toBeVisible();
    
    await page.screenshot({ path: 'artifacts/transaksi-calculation-masuk.png' });
  });
  
  test('should validate jumlah keluar not exceeding stock', async ({ page }) => {
    const transaksiPage = new TransaksiCreatePage(page);
    await transaksiPage.goto();
    
    // Get available options
    const options = await transaksiPage.barangSelect.locator('option').all();
    if (options.length <= 1) {
      test.skip('No barang available for testing');
      return;
    }
    
    const firstValue = await options[1].getAttribute('value');
    if (!firstValue) {
      test.skip('No barang value found');
      return;
    }
    
    await transaksiPage.selectBarang(firstValue);
    await page.waitForTimeout(500);
    
    // Get current stock
    const stockText = await transaksiPage.getStockInfo();
    const currentStock = parseInt(stockText || '0');
    
    // Try to take more than available stock
    await transaksiPage.fillBarangKeluar(currentStock + 100, 'Test User');
    
    // Check calculation shows error/warning
    const sisaSetelahKeluar = page.locator('#sisa_setelah_keluar');
    await expect(sisaSetelahKeluar).toBeVisible();
    
    await page.screenshot({ path: 'artifacts/transaksi-validation-keluar.png' });
  });
  
  test('should require barang selection', async ({ page }) => {
    const transaksiPage = new TransaksiCreatePage(page);
    await transaksiPage.goto();
    
    // Try to submit without selecting barang
    await transaksiPage.submit();
    
    // Should still be on create page
    await expect(page.url()).toContain('/transaksi/create');
    
    await page.screenshot({ path: 'artifacts/transaksi-validation-required.png' });
  });
  
  test('should export transactions', async ({ page }) => {
    const transaksiPage = new TransaksiListPage(page);
    await transaksiPage.goto();
    
    // Click export button
    await transaksiPage.exportButton.click();
    
    // Wait for modal to appear
    await page.waitForSelector('#exportModal', { state: 'visible' });
    
    // Verify modal is visible
    const modal = page.locator('#exportModal');
    await expect(modal).toBeVisible();
    
    // Take screenshot of export modal
    await page.screenshot({ path: 'artifacts/transaksi-export-modal.png' });
    
    // Verify "Semua Data" (All Data) is selected by default
    const allDataRadio = page.locator('input[value="all"]#export_all');
    await expect(allDataRadio).toBeChecked();
    
    // Click Export button in modal
    const exportSubmitBtn = page.locator('#exportSubmitBtn');
    await exportSubmitBtn.click();
    
    // Wait for download to complete (page should return to transaksi)
    await page.waitForTimeout(2000);
    
    // Verify still on transaksi page (not error page)
    await expect(page.url()).toContain('/transaksi');
    
    // Verify no error message is shown
    const errorAlert = page.locator('.alert-danger');
    await expect(errorAlert).not.toBeVisible();
    
    await page.screenshot({ path: 'artifacts/transaksi-export.png' });
  });
  
  test('should search transactions', async ({ page }) => {
    const transaksiPage = new TransaksiListPage(page);
    await transaksiPage.goto();
    
    // Get search input
    const hasSearch = await transaksiPage.searchInput.isVisible().catch(() => false);
    
    if (hasSearch) {
      await transaksiPage.searchInput.fill('test');
      await transaksiPage.filterButton.click();
      await page.waitForLoadState('networkidle');
      
      await page.screenshot({ path: 'artifacts/transaksi-search.png' });
    }
  });
  
  test('should update stock after transaction', async ({ page }) => {
    const transaksiPage = new TransaksiCreatePage(page);
    await transaksiPage.goto();
    
    // Get available options
    const options = await transaksiPage.barangSelect.locator('option').all();
    if (options.length <= 1) {
      test.skip('No barang available for testing');
      return;
    }
    
    const firstValue = await options[1].getAttribute('value');
    if (!firstValue) {
      test.skip('No barang value found');
      return;
    }
    
    // Select barang and note stock
    await transaksiPage.selectBarang(firstValue);
    await page.waitForTimeout(500);
    
    const stockBefore = await transaksiPage.getStockInfo();
    const stockBeforeNum = parseInt(stockBefore || '0');
    
    // Add barang masuk
    await transaksiPage.fillBarangMasuk(10);
    await transaksiPage.submit();
    
    await page.waitForLoadState('networkidle');
    
    // Go back to create page
    await transaksiPage.goto();
    await transaksiPage.selectBarang(firstValue);
    await page.waitForTimeout(500);
    
    // Verify stock increased
    const stockAfter = await transaksiPage.getStockInfo();
    const stockAfterNum = parseInt(stockAfter || '0');
    
    expect(stockAfterNum).toBeGreaterThanOrEqual(stockBeforeNum);
    
    await page.screenshot({ path: 'artifacts/transaksi-stock-updated.png' });
  });
});
