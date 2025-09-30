<template>
    <div class="min-h-screen bg-white text-gray-900">
        <main class="mx-auto max-w-3xl px-6 py-10">
            <h1 class="mb-6 text-3xl font-bold">Видалення даних користувача</h1>
            <p class="mb-8 text-sm text-gray-500">
                Останнє оновлення: {{ lastUpdated }}
            </p>

            <section class="space-y-4">
                <p>
                    Ви маєте право на видалення своїх персональних даних. Нижче
                    описано способи подання запиту та порядок його опрацювання.
                </p>

                <h2 class="mt-6 text-xl font-semibold">
                    Як подати запит на видалення
                </h2>
                <ul class="list-disc space-y-2 pl-6">
                    <li>
                        Через електронну пошту: надішліть лист з темою
                        «Видалення даних» на адресу
                        <span class="font-medium">admin@carbeat.online</span>,
                        вказавши номер телефону, з яким пов'язаний акаунт, та
                        суть запиту.
                    </li>
                    <li>
                        Через застосунок/сайт: якщо доступно, скористайтеся
                        кнопкою «Видалити акаунт» у налаштуваннях профілю.
                    </li>
                </ul>

                <h2 class="mt-6 text-xl font-semibold">Підтвердження особи</h2>
                <p>
                    З міркувань безпеки ми можемо попросити підтвердити номер
                    телефону (OTP) або інший доказ належності акаунта, перш ніж
                    виконати запит.
                </p>

                <h2 class="mt-6 text-xl font-semibold">Що буде видалено</h2>
                <ul class="list-disc space-y-2 pl-6">
                    <li>Профіль користувача та контактні дані.</li>
                    <li>
                        Вміст, створений користувачем (наприклад, відгуки), якщо
                        це не суперечить вимогам законодавства.
                    </li>
                    <li>
                        Токени сповіщень/аналітики, пов'язані з вашим пристроєм.
                    </li>
                </ul>
                <p class="text-sm text-gray-600">
                    Частина даних може зберігатися у знеособленому вигляді для
                    аналітики або у журналах у межах строків, визначених
                    законом.
                </p>

                <h2 class="mt-6 text-xl font-semibold">Строки виконання</h2>
                <p>
                    Ми опрацьовуємо запити на видалення без зайвої затримки і,
                    як правило, завершуємо протягом 30 днів.
                </p>

                <h2 class="mt-6 text-xl font-semibold">Контакти</h2>
                <p>
                    З питань приватності та видалення даних звертайтесь:
                    <span class="font-medium">admin@carbeat.online</span>
                </p>
            </section>
        </main>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const lastUpdated = ref(new Date().toISOString().slice(0, 10));
</script>
