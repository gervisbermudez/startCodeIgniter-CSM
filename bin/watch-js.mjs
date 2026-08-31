import { copyFileSync, mkdirSync, readdirSync, watch } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const srcDir = join(root, 'resources/js');
const destDir = join(root, 'public/js');

function copyOne(name) {
  if (!name || !name.endsWith('.js')) {
    return;
  }
  copyFileSync(join(srcDir, name), join(destDir, name));
  console.log('[copy-js]', name);
}

mkdirSync(destDir, { recursive: true });
for (const name of readdirSync(srcDir)) {
  copyOne(name);
}

watch(srcDir, (event, filename) => {
  if (filename) {
    copyOne(filename);
  }
});

console.log('[copy-js] watching resources/js → public/js');
