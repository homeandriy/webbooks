import {mkdirSync, renameSync, writeFileSync} from 'node:fs';
import {randomUUID} from 'node:crypto';
import {dirname, resolve} from 'node:path';

const tagPath = resolve('dist/.vite/build-tag');
const tag = `${new Date().toISOString().replace(/[-:.TZ]/g, '')}-${randomUUID()}`;

mkdirSync(dirname(tagPath), {recursive: true});

const temporaryPath = `${tagPath}.${process.pid}.tmp`;
writeFileSync(temporaryPath, `${tag}\n`, 'utf8');
renameSync(temporaryPath, tagPath);

console.log(`Generated Vite build tag: ${tag}`);
