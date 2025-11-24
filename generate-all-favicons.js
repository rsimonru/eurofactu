/**
 * Script completo para generar TODOS los favicons de Eurofactu
 * incluyendo el archivo .ico
 *
 * Uso: node generate-all-favicons.js
 */

import sharp from 'sharp';
import toIco from 'to-ico';
import { readFileSync, writeFileSync, unlinkSync } from 'fs';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const publicDir = join(__dirname, 'public');
const svgPath = join(publicDir, 'favicon.svg');

console.log('='.repeat(60));
console.log('GENERANDO TODOS LOS FAVICONS DE EUROFACTU');
console.log('='.repeat(60));

async function generateAllFavicons() {
    try {
        // Leer SVG
        const svgBuffer = readFileSync(svgPath);

        // Generar Apple Touch Icon (180x180)
        console.log('\n📱 Generando apple-touch-icon.png (180x180)...');
        await sharp(svgBuffer)
            .resize(180, 180)
            .png()
            .toFile(join(publicDir, 'apple-touch-icon.png'));
        console.log('✅ apple-touch-icon.png');

        // Generar icon-192.png para Android
        console.log('\n🤖 Generando icon-192.png (192x192)...');
        await sharp(svgBuffer)
            .resize(192, 192)
            .png()
            .toFile(join(publicDir, 'icon-192.png'));
        console.log('✅ icon-192.png');

        // Generar icon-512.png para Android
        console.log('\n🤖 Generando icon-512.png (512x512)...');
        await sharp(svgBuffer)
            .resize(512, 512)
            .png()
            .toFile(join(publicDir, 'icon-512.png'));
        console.log('✅ icon-512.png');

        // Generar favicon.ico con múltiples tamaños
        console.log('\n🖼️  Generando favicon.ico...');

        // Crear buffers de diferentes tamaños para el ICO
        const png32 = await sharp(svgBuffer)
            .resize(32, 32)
            .png()
            .toBuffer();

        const png16 = await sharp(svgBuffer)
            .resize(16, 16)
            .png()
            .toBuffer();

        // Crear ICO con ambos tamaños
        const icoBuffer = await toIco([png32, png16]);
        writeFileSync(join(publicDir, 'favicon.ico'), icoBuffer);
        console.log('✅ favicon.ico (32x32 + 16x16)');

        console.log('\n' + '='.repeat(60));
        console.log('✅ GENERACIÓN COMPLETADA EXITOSAMENTE');
        console.log('='.repeat(60));

        console.log('\n📦 Archivos generados en public/:');
        console.log('   ✅ favicon.svg          - Favicon moderno (dark mode)');
        console.log('   ✅ favicon.ico          - Compatibilidad navegadores antiguos');
        console.log('   ✅ apple-touch-icon.png - iOS home screen (180x180)');
        console.log('   ✅ icon-192.png         - Android/PWA (192x192)');
        console.log('   ✅ icon-512.png         - Android/PWA (512x512)');
        console.log('   ✅ site.webmanifest     - PWA manifest');

        console.log('\n📝 Referencias actualizadas en:');
        console.log('   ✅ resources/views/partials/head.blade.php');

        console.log('\n' + '='.repeat(60));
        console.log('🎉 ¡TODOS LOS FAVICONS ESTÁN LISTOS!');
        console.log('='.repeat(60));
        console.log('\nPuedes eliminar los archivos temporales:');
        console.log('- public/favicon-32.png');
        console.log('- public/favicon-16.png');
        console.log('\n💡 El favicon se mostrará automáticamente en navegadores');
        console.log('   después de recargar la página (Ctrl+F5)');
        console.log('='.repeat(60) + '\n');

    } catch (error) {
        console.error('\n❌ Error generando favicons:', error.message);
        console.error(error);
        process.exit(1);
    }
}

generateAllFavicons();
