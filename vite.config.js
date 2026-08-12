import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
    ],
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                'asset-manager-styles': 'resources/css/asset-manager.css',
                'asset-manager': 'resources/js/asset-manager.js'
            },
            output: {
                entryFileNames: 'js/[name].js',
                assetFileNames: 'css/asset-manager.[ext]'
            }
        }
    }
});
