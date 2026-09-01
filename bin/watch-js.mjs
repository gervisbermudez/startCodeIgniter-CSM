import { copyFileSync, mkdirSync, readdirSync, watch, existsSync } from 'fs';
import { spawnSync } from 'child_process';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const srcDir = join(root, 'resources/js');
const destDir = join(root, 'public/js');
const copyJs = join(root, 'bin/copy-js.sh');

function rebuildBundles() {
  spawnSync('bash', [copyJs], { stdio: 'inherit' });
}

mkdirSync(destDir, { recursive: true });
rebuildBundles();

watch(srcDir, (event, filename) => {
  if (filename && filename.endsWith('.js')) {
    copyFileSync(join(srcDir, filename), join(destDir, filename));
    console.log('[copy-js]', filename);
    rebuildBundles();
  }
});

const componentsDir = join(root, 'resources/components');
if (existsSync(componentsDir)) {
  watch(componentsDir, { recursive: true }, (event, filename) => {
    if (filename && filename.endsWith('.js')) {
      console.log('[copy-js] component', filename);
      rebuildBundles();
    }
  });
}

console.log('[copy-js] watching resources/js + resources/components');
