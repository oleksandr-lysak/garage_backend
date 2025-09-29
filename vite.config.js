import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => {
    process.env.NODE_ENV = mode;

    const isProd = mode === 'production';

    return {
        base: isProd ? '/build/' : '/',
        server: {
            https: false,
            host: '127.0.0.1',
            port: 5173,
            strictPort: true,
            cors: true,
            hmr: {
                host: '127.0.0.1',
                port: 5173,
                protocol: 'ws',
                clientPort: 5173,
            },
        },
        plugins: [
            laravel({
                input: [
                    'resources/js/app.ts',
                    'resources/css/app.css',
                ],
                ssr: 'resources/js/ssr.ts',
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],
        define: {
            'process.env.NODE_ENV': JSON.stringify(mode),
        },
    };
});
