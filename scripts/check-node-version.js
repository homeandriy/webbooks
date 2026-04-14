#!/usr/bin/env node

const major = Number.parseInt(process.versions.node.split('.')[0], 10);

if (!Number.isInteger(major)) {
  console.error('❌ Unable to detect Node.js version. Please use Node.js 20 LTS (minimum supported: 18).');
  process.exit(1);
}

if (major < 18) {
  console.error(
    `❌ Unsupported Node.js version ${process.version}.\n` +
      'Please upgrade to Node.js 20 LTS (minimum supported: 18) and rerun:\n' +
      '  npm ci\n' +
      '  npm run build'
  );
  process.exit(1);
}

if (major > 20) {
  console.warn(
    `⚠️ Detected Node.js ${process.version}. This project is validated on Node.js 20 LTS (minimum supported: 18).`
  );
}

console.log(`✅ Node.js ${process.version} satisfies the minimum requirement (>=18).`);
