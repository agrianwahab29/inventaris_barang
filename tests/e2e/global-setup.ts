import { FullConfig } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

/**
 * Global setup for E2E tests
 * This runs once before all test suites
 */
async function globalSetup(config: FullConfig) {
  console.log('🔧 Running global setup...');
  
  // Create artifacts directory
  const artifactsDir = path.join(__dirname, '..', '..', 'artifacts');
  if (!fs.existsSync(artifactsDir)) {
    fs.mkdirSync(artifactsDir, { recursive: true });
  }
  
  // Create playwright-report directory
  const reportDir = path.join(__dirname, '..', '..', 'playwright-report');
  if (!fs.existsSync(reportDir)) {
    fs.mkdirSync(reportDir, { recursive: true });
  }
  
  // Log test environment
  console.log('📍 Base URL:', config.projects[0]?.use?.baseURL || 'http://localhost:8000');
  console.log('✅ Global setup complete');
}

export default globalSetup;
