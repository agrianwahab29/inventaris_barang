import { FullConfig } from '@playwright/test';

/**
 * Global teardown for E2E tests
 * This runs once after all test suites
 */
async function globalTeardown(config: FullConfig) {
  console.log('🧹 Running global teardown...');
  
  // Add any cleanup logic here if needed
  // e.g., cleaning up test data, closing connections, etc.
  
  console.log('✅ Global teardown complete');
}

export default globalTeardown;
