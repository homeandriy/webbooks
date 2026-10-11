#!/usr/bin/env node

const [major, minor] = process.versions.node.split('.').map(Number);

if (!Number.isInteger(major)) {
  console.error('Unable to detect Node.js version. Use Node.js 22.13 or newer.');
  process.exit(1);
}

if (major < 22 || (major === 22 && minor < 13)) {
  console.error(
    `❌ Unsupported Node.js version ${process.version}.\n` +
      'Please upgrade to Node.js 22.13 or newer and rerun:\n' +
      '  npm ci\n' +
      '  npm run build'
  );
  process.exit(1);
}

if (major > 22) {
  console.warn(
    `Detected Node.js ${process.version}. The build is tested on Node.js 22.`
  );
}

console.log(`Node.js ${process.version} satisfies the minimum requirement (>=22.13).`);
