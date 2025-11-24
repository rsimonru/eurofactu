/**
 * Script automatizado para generar todos los favicons de Eurofactu
 * usando sharp para procesamiento de imágenes
 *
 * Uso: node generate-favicons-auto.js
 */

import sharp from 'sharp';
import { readFileSync, writeFileSync, unlinkSync } from 'fs';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const publicDir = join(__dirname, 'public');
const svgPath = join(publicDir, 'favicon.svg');

console.log('='.repeat(60));
console.log('GENERANDO FAVICONS AUTOMÁTICAMENTE');
console.log('='.repeat(60));

async function generateFavicons() {
    try {
        // Leer SVG
        const svgBuffer = readFileSync(svgPath);

        // Generar Apple Touch Icon (180x180)
        console.log('\n📱 Generando apple-touch-icon.png (180x180)...');
        await sharp(svgBuffer)
            .resize(180, 180)
            .png()
            .toFile(join(publicDir, 'apple-touch-icon.png'));
        console.log('✅ apple-touch-icon.png creado');

        // Generar icon-192.png para Android
        console.log('\n🤖 Generando icon-192.png (192x192)...');
        await sharp(svgBuffer)
            .resize(192, 192)
            .png()
            .toFile(join(publicDir, 'icon-192.png'));
        console.log('✅ icon-192.png creado');

        // Generar icon-512.png para Android
        console.log('\n🤖 Generando icon-512.png (512x512)...');
        await sharp(svgBuffer)
            .resize(512, 512)
            .png()
            .toFile(join(publicDir, 'icon-512.png'));
        console.log('✅ icon-512.png creado');

        // Generar favicon ICO (necesitamos crear 32x32 y 16x16)
        console.log('\n🖼️  Generando favicon.ico...');

        // Crear 32x32
        const png32Path = join(publicDir, 'favicon-32.png');
        await sharp(svgBuffer)
            .resize(32, 32)
            .png()
            .toFile(png32Path);

        // Crear 16x16
        const png16Path = join(publicDir, 'favicon-16.png');
        await sharp(svgBuffer)
            .resize(16, 16)
            .png()
            .toFile(png16Path);

        console.log('⚠️  Nota: Los archivos PNG de 32x32 y 16x16 se han creado.');
        console.log('   Para crear favicon.ico, necesitas combinarlos con una herramienta');
        console.log('   como ImageMagick o usar un conversor online:');
        console.log('   https://www.icoconverter.com/');
        console.log('\n   Sube favicon-32.png y favicon-16.png para crear favicon.ico');
        console.log('   Los archivos temporales se mantienen para tu uso.');

        console.log('\n' + '='.repeat(60));
        console.log('✅ GENERACIÓN COMPLETADA');
        console.log('='.repeat(60));

        console.log('\nArchivos generados:');
        console.log('✅ public/favicon.svg');
        console.log('✅ public/apple-touch-icon.png');
        console.log('✅ public/icon-192.png');
        console.log('✅ public/icon-512.png');
        console.log('✅ public/favicon-32.png (temporal para ICO)');
        console.log('✅ public/favicon-16.png (temporal para ICO)');
        console.log('✅ public/site.webmanifest');

        console.log('\nPendiente:');
        console.log('⏳ public/favicon.ico - Combina favicon-32.png y favicon-16.png');
        console.log('   en https://www.icoconverter.com/ o similar');

        console.log('\n' + '='.repeat(60));
        console.log('El favicon.svg ya funciona en navegadores modernos.');
        console.log('Los PNG e ICO son para máxima compatibilidad.');
        console.log('='.repeat(60) + '\n');

    } catch (error) {
        console.error('\n❌ Error generando favicons:', error.message);
        process.exit(1);
    }
}

generateFavicons();
