#!/usr/bin/env node

const [ major, minor ] = process.versions.node.split( '.' ).map( Number );
const minimumMajor = 20;
const minimumMinor = 19;

if ( ! Number.isInteger( major ) || ! Number.isInteger( minor ) ) {
  console.error( '❌ Unable to detect Node.js version. Please use Node.js 20.19+.' );
  process.exit(1);
}

if ( major < minimumMajor || ( minimumMajor === major && minor < minimumMinor ) ) {
  console.error(
    `❌ Unsupported Node.js version ${ process.version }.\n` +
      'Please upgrade to Node.js 20.19+ and rerun:\n' +
      '  npm ci\n' +
      '  npm run build'
  );
  process.exit(1);
}

if ( major > minimumMajor ) {
  console.warn(
    `⚠️ Detected Node.js ${ process.version }. This project is validated on Node.js 20.19+.`
  );
}

console.log( `✅ Node.js ${ process.version } satisfies the minimum requirement (>=20.19.0).` );
