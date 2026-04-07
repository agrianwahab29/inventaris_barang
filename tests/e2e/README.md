# E2E Tests for Sistem Inventaris Kantor

Comprehensive end-to-end tests for the Laravel Inventory System using Playwright.

## 📁 Test Structure

```
tests/e2e/
├── auth/
│   └── login.spec.ts       # Authentication flow tests
├── dashboard/
│   └── dashboard.spec.ts   # Dashboard navigation and stats
├── barang/
│   └── barang.spec.ts      # Item management CRUD tests
├── transaksi/
│   └── transaksi.spec.ts   # Transaction flow tests
├── ruangan/
│   └── ruangan.spec.ts     # Room management tests
├── pages/
│   └── index.ts            # Page Object Models
├── fixtures.ts             # Test fixtures and helpers
├── global-setup.ts         # Global test setup
└── global-teardown.ts      # Global test teardown
```

## 🚀 Quick Start

### Install Dependencies

```bash
# Install Playwright (already done during setup)
npm install -D @playwright/test
npx playwright install chromium
```

### Run Tests

```bash
# Run all tests
npx playwright test

# Run specific test file
npx playwright test tests/e2e/auth/login.spec.ts

# Run tests in headed mode (see browser)
npx playwright test --headed

# Debug mode
npx playwright test --debug

# Run with specific browser
npx playwright test --project=chromium
```

## 🧪 Test Coverage

### Critical User Journeys

| Feature | Test File | Status |
|---------|-----------|--------|
| Login | `auth/login.spec.ts` | ✅ Complete |
| Dashboard | `dashboard/dashboard.spec.ts` | ✅ Complete |
| Barang (Items) | `barang/barang.spec.ts` | ✅ Complete |
| Transaksi (Transactions) | `transaksi/transaksi.spec.ts` | ✅ Complete |
| Ruangan (Rooms) | `ruangan/ruangan.spec.ts` | ✅ Complete |

## 📊 Test Users

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | admin123 |
| User | user | user123 |

## 🔧 Configuration

Edit `playwright.config.ts` to customize:
- Base URL
- Browser settings
- Screenshot/Video settings
- Timeout values

## 📸 Artifacts

Test artifacts are saved to:
- `artifacts/` - Screenshots
- `playwright-report/` - HTML reports and traces
- `playwright-report/results.xml` - JUnit results

## 📝 Best Practices

1. **Page Object Model**: All selectors are defined in `pages/index.ts`
2. **Fixtures**: Use `test` from `fixtures.ts` for authenticated tests
3. **Screenshots**: Each test takes a screenshot for debugging
4. **Isolation**: Tests are independent and clean up after themselves

## 🐛 Debugging Failed Tests

```bash
# Show HTML report
npx playwright show-report

# Show trace viewer
npx playwright show-trace playwright-report/trace.zip
```

## 🔐 Security Notes

- Tests use dedicated test users
- Never run tests against production
- Credentials are hardcoded for testing only

## 📚 Documentation

- [Playwright Docs](https://playwright.dev/)
- [Best Practices](https://playwright.dev/docs/best-practices)
- [Selectors Guide](https://playwright.dev/docs/selectors)
