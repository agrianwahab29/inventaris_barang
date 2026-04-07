import { Page, Locator, expect } from '@playwright/test';

/**
 * Page Object Model for Login Page
 * Handles authentication flows
 */
export class LoginPage {
  readonly page: Page;
  readonly usernameInput: Locator;
  readonly passwordInput: Locator;
  readonly submitButton: Locator;
  readonly errorMessage: Locator;
  readonly loginCard: Locator;

  constructor(page: Page) {
    this.page = page;
    this.usernameInput = page.locator('input[name="username"]');
    this.passwordInput = page.locator('input[name="password"]');
    this.submitButton = page.locator('button[type="submit"]');
    this.errorMessage = page.locator('.alert-danger');
    this.loginCard = page.locator('.login-card');
  }

  /**
   * Navigate to login page
   */
  async goto() {
    await this.page.goto('/login');
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Perform login with credentials
   */
  async login(username: string, password: string) {
    await this.usernameInput.fill(username);
    await this.passwordInput.fill(password);
    await this.submitButton.click();
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Verify login page is loaded
   */
  async verifyPageLoaded() {
    await expect(this.loginCard).toBeVisible();
    await expect(this.usernameInput).toBeVisible();
    await expect(this.passwordInput).toBeVisible();
    await expect(this.submitButton).toBeVisible();
    await expect(this.submitButton).toContainText('Masuk');
  }

  /**
   * Verify error message is shown
   */
  async verifyErrorMessage(expectedText?: string) {
    await expect(this.errorMessage).toBeVisible();
    if (expectedText) {
      await expect(this.errorMessage).toContainText(expectedText);
    }
  }

  /**
   * Logout the current user
   */
  async logout() {
    await this.page.goto('/logout', { method: 'post' } as any);
    await this.page.waitForLoadState('networkidle');
  }
}

/**
 * Page Object Model for Dashboard
 * Central hub page with statistics and navigation
 */
export class DashboardPage {
  readonly page: Page;
  readonly welcomeBanner: Locator;
  readonly statCards: Locator;
  readonly sidebar: Locator;
  readonly sidebarLinks: { [key: string]: Locator };
  readonly chart: Locator;
  readonly quickActions: Locator;
  readonly stockAlerts: Locator;
  readonly recentTransactions: Locator;

  constructor(page: Page) {
    this.page = page;
    this.welcomeBanner = page.locator('.welcome-banner');
    this.statCards = page.locator('.stat-card');
    this.sidebar = page.locator('.sidebar');
    this.chart = page.locator('#transaksiChart');
    this.quickActions = page.locator('.quick-action');
    this.stockAlerts = page.locator('.alert-item');
    this.recentTransactions = page.locator('.transaction-item');
    
    this.sidebarLinks = {
      dashboard: page.locator('.sidebar .nav-link[href*="dashboard"]'),
      barang: page.locator('.sidebar .nav-link[href*="barang"]:not([href*="transaksi"])'),
      ruangan: page.locator('.sidebar .nav-link[href*="ruangan"]'),
      transaksiCreate: page.locator('.sidebar .nav-link[href*="transaksi/create"]'),
      transaksiIndex: page.locator('.sidebar .nav-link[href*="transaksi"]:not([href*="create"])'),
      quarterlyStock: page.locator('.sidebar .nav-link[href*="quarterly-stock"]'),
      suratTandaTerima: page.locator('.sidebar .nav-link[href*="surat-tanda-terima"]'),
      users: page.locator('.sidebar .nav-link[href*="users"]'),
    };
  }

  /**
   * Navigate to dashboard
   */
  async goto() {
    await this.page.goto('/dashboard');
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Verify dashboard is loaded
   */
  async verifyPageLoaded() {
    await expect(this.welcomeBanner).toBeVisible();
    await expect(this.sidebar).toBeVisible();
    await expect(this.statCards.first()).toBeVisible();
  }

  /**
   * Click sidebar navigation link
   */
  async clickSidebarLink(linkName: string) {
    await this.sidebarLinks[linkName].click();
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Get dashboard stats
   */
  async getStats() {
    const statValues = await this.statCards.locator('.stat-value').allTextContents();
    const statLabels = await this.statCards.locator('.stat-label').allTextContents();
    return { values: statValues, labels: statLabels };
  }

  /**
   * Verify welcome message contains user name
   */
  async verifyWelcomeMessage(userName: string) {
    await expect(this.welcomeBanner).toContainText('Selamat Datang');
    await expect(this.welcomeBanner).toContainText(userName);
  }

  /**
   * Navigate via quick action
   */
  async clickQuickAction(actionText: string) {
    await this.quickActions.filter({ hasText: actionText }).click();
    await this.page.waitForLoadState('networkidle');
  }
}

/**
 * Page Object Model for Barang (Items) List Page
 */
export class BarangListPage {
  readonly page: Page;
  readonly searchInput: Locator;
  readonly filterButton: Locator;
  readonly addButton: Locator;
  readonly exportButton: Locator;
  readonly dataTable: Locator;
  readonly tableRows: Locator;
  readonly categoryFilter: Locator;
  readonly statusFilter: Locator;
  readonly statsCards: Locator;

  constructor(page: Page) {
    this.page = page;
    this.searchInput = page.locator('input[name="search"]');
    this.filterButton = page.locator('button[type="submit"]:has-text("Filter")');
    this.addButton = page.locator('a[href*="barang/create"]');
    this.exportButton = page.locator('a:has-text("Export Excel")');
    this.dataTable = page.locator('.data-card table');
    this.tableRows = page.locator('.data-card tbody tr');
    this.categoryFilter = page.locator('select[name="kategori"]');
    this.statusFilter = page.locator('select[name="status"]');
    this.statsCards = page.locator('.card-body');
  }

  /**
   * Navigate to barang list
   */
  async goto() {
    await this.page.goto('/barang');
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Verify page is loaded
   */
  async verifyPageLoaded() {
    await expect(this.dataTable).toBeVisible();
    await expect(this.addButton).toBeVisible();
  }

  /**
   * Search for barang
   */
  async search(query: string) {
    await this.searchInput.fill(query);
    await this.filterButton.click();
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Click add button
   */
  async clickAdd() {
    await this.addButton.click();
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Click view button for specific row
   */
  async clickView(rowIndex: number = 0) {
    const viewButton = this.tableRows.nth(rowIndex).locator('a:has(.fa-eye)');
    await viewButton.click();
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Get row count
   */
  async getRowCount() {
    return await this.tableRows.count();
  }

  /**
   * Filter by status
   */
  async filterByStatus(status: string) {
    await this.statusFilter.selectOption(status);
    await this.filterButton.click();
    await this.page.waitForLoadState('networkidle');
  }
}

/**
 * Page Object Model for Barang Create/Edit Page
 */
export class BarangFormPage {
  readonly page: Page;
  readonly namaBarangInput: Locator;
  readonly kategoriSelect: Locator;
  readonly satuanInput: Locator;
  readonly stokInput: Locator;
  readonly stokMinimumInput: Locator;
  readonly catatanInput: Locator;
  readonly ruanganSelect: Locator;
  readonly saveButton: Locator;
  readonly cancelButton: Locator;

  constructor(page: Page) {
    this.page = page;
    this.namaBarangInput = page.locator('input[name="nama_barang"], #nama_barang');
    this.kategoriSelect = page.locator('select[name="kategori"], #kategori');
    this.satuanInput = page.locator('input[name="satuan"], #satuan');
    this.stokInput = page.locator('input[name="stok"], #stok');
    this.stokMinimumInput = page.locator('input[name="stok_minimum"], #stok_minimum');
    this.catatanInput = page.locator('textarea[name="catatan"], #catatan');
    this.ruanganSelect = page.locator('select[name="ruangan_id"], #ruangan_id');
    this.saveButton = page.locator('button[type="submit"]:has-text("Simpan")');
    this.cancelButton = page.locator('a:has-text("Batal")');
  }

  /**
   * Fill barang form
   */
  async fillForm(data: {
    namaBarang?: string;
    kategori?: string;
    satuan?: string;
    stok?: string;
    stokMinimum?: string;
    catatan?: string;
    ruanganId?: string;
  }) {
    if (data.namaBarang) await this.namaBarangInput.fill(data.namaBarang);
    if (data.kategori) await this.kategoriSelect.selectOption(data.kategori);
    if (data.satuan) await this.satuanInput.fill(data.satuan);
    if (data.stok) await this.stokInput.fill(data.stok);
    if (data.stokMinimum) await this.stokMinimumInput.fill(data.stokMinimum);
    if (data.catatan) await this.catatanInput.fill(data.catatan);
    if (data.ruanganId) await this.ruanganSelect.selectOption(data.ruanganId);
  }

  /**
   * Submit form
   */
  async submit() {
    await this.saveButton.click();
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Cancel form
   */
  async cancel() {
    await this.cancelButton.click();
    await this.page.waitForLoadState('networkidle');
  }
}

/**
 * Page Object Model for Transaksi (Transaction) Create Page
 */
export class TransaksiCreatePage {
  readonly page: Page;
  readonly barangSelect: Locator;
  readonly jumlahMasukInput: Locator;
  readonly jumlahKeluarInput: Locator;
  readonly tanggalMasukInput: Locator;
  readonly tanggalKeluarInput: Locator;
  readonly ruanganSelect: Locator;
  readonly namaPengambilInput: Locator;
  readonly infoBox: Locator;
  readonly stokInfo: Locator;
  readonly saveButton: Locator;
  readonly cancelButton: Locator;
  readonly form: Locator;

  constructor(page: Page) {
    this.page = page;
    this.barangSelect = page.locator('#barang_id');
    this.jumlahMasukInput = page.locator('#jumlah_masuk');
    this.jumlahKeluarInput = page.locator('#jumlah_keluar');
    this.tanggalMasukInput = page.locator('input[name="tanggal_masuk"]');
    this.tanggalKeluarInput = page.locator('input[name="tanggal_keluar"]');
    this.ruanganSelect = page.locator('#ruangan_id');
    this.namaPengambilInput = page.locator('#nama_pengambil');
    this.infoBox = page.locator('#infoBoxBarang');
    this.stokInfo = page.locator('#infoStok');
    this.saveButton = page.locator('button[type="submit"]:has-text("Simpan")');
    this.cancelButton = page.locator('.btn-cancel');
    this.form = page.locator('#formTransaksi');
  }

  /**
   * Navigate to transaksi create
   */
  async goto() {
    await this.page.goto('/transaksi/create');
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Select barang
   */
  async selectBarang(barangId: string) {
    await this.barangSelect.selectOption(barangId);
    await this.page.waitForTimeout(500); // Wait for AJAX update
  }

  /**
   * Fill transaction form for barang masuk
   */
  async fillBarangMasuk(jumlah: number, tanggal?: string) {
    await this.jumlahMasukInput.fill(jumlah.toString());
    if (tanggal) {
      await this.tanggalMasukInput.fill(tanggal);
    }
  }

  /**
   * Fill transaction form for barang keluar
   */
  async fillBarangKeluar(jumlah: number, namaPengambil: string, ruanganId?: string, tanggal?: string) {
    await this.jumlahKeluarInput.fill(jumlah.toString());
    await this.namaPengambilInput.fill(namaPengambil);
    if (ruanganId) {
      await this.ruanganSelect.selectOption(ruanganId);
    }
    if (tanggal) {
      await this.tanggalKeluarInput.fill(tanggal);
    }
  }

  /**
   * Submit form
   */
  async submit() {
    await this.saveButton.click();
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Get displayed stock info
   */
  async getStockInfo() {
    await expect(this.infoBox).toBeVisible();
    return await this.stokInfo.textContent();
  }

  /**
   * Verify page loaded
   */
  async verifyPageLoaded() {
    await expect(this.form).toBeVisible();
    await expect(this.barangSelect).toBeVisible();
  }
}

/**
 * Page Object Model for Transaksi List Page
 */
export class TransaksiListPage {
  readonly page: Page;
  readonly dataTable: Locator;
  readonly tableRows: Locator;
  readonly exportButton: Locator;
  readonly searchInput: Locator;
  readonly filterButton: Locator;

  constructor(page: Page) {
    this.page = page;
    this.dataTable = page.locator('table');
    this.tableRows = page.locator('tbody tr');
    this.exportButton = page.locator('#btnExportModal');
    this.searchInput = page.locator('input[name="search"]');
    this.filterButton = page.locator('button:has-text("Filter")');
  }

  /**
   * Navigate to transaksi list
   */
  async goto() {
    await this.page.goto('/transaksi');
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Verify page loaded
   */
  async verifyPageLoaded() {
    await expect(this.dataTable).toBeVisible();
  }
}

/**
 * Page Object Model for Ruangan (Rooms) Page
 */
export class RuanganPage {
  readonly page: Page;
  readonly dataTable: Locator;
  readonly tableRows: Locator;
  readonly addButton: Locator;

  constructor(page: Page) {
    this.page = page;
    this.dataTable = page.locator('.data-card table, table');
    this.tableRows = page.locator('tbody tr');
    this.addButton = page.locator('a[href*="ruangan/create"]');
  }

  /**
   * Navigate to ruangan list
   */
  async goto() {
    await this.page.goto('/ruangan');
    await this.page.waitForLoadState('networkidle');
  }

  /**
   * Verify page loaded
   */
  async verifyPageLoaded() {
    await expect(this.dataTable).toBeVisible();
  }
}
