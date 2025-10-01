<template>
    <Head title="API Документація" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">API Документація для мобільного додатку</h1>

                    <div class="space-y-8">
                        <!-- Authentication Section -->
                        <section>
                            <h2 class="text-xl font-semibold mb-4 text-blue-600">🔐 Авторизація</h2>

                            <div class="bg-gray-50 p-4 rounded-lg space-y-4">
                                <div>
                                    <h3 class="font-semibold text-green-600">1. Запит OTP коду</h3>
                                    <div class="bg-black text-green-400 p-3 rounded font-mono text-sm mt-2">
                                        <div>POST /api/auth/request-otp</div>
                                        <div class="text-gray-300">Content-Type: application/json</div>
                                        <div class="mt-2">
                                            {<br>
                                            &nbsp;&nbsp;"phone": "+380501234567"<br>
                                            }
                                        </div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <strong>Відповідь:</strong> { "message": "OTP sent", "needs_registration": false }
                                    </div>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-green-600">2. Підтвердження OTP коду</h3>
                                    <div class="bg-black text-green-400 p-3 rounded font-mono text-sm mt-2">
                                        <div>POST /api/auth/verify-otp</div>
                                        <div class="text-gray-300">Content-Type: application/json</div>
                                        <div class="mt-2">
                                            {<br>
                                            &nbsp;&nbsp;"phone": "+380501234567",<br>
                                            &nbsp;&nbsp;"sms_code": "1234"<br>
                                            }
                                        </div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <strong>Відповідь:</strong><br>
                                        {<br>
                                        &nbsp;&nbsp;"user": { "id": 1, "name": "Master 4567", "phone": "+380501234567" },<br>
                                        &nbsp;&nbsp;"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",<br>
                                        &nbsp;&nbsp;"refresh_token": "abc123def456...",<br>
                                        &nbsp;&nbsp;"expires_in": 3600<br>
                                        }
                                    </div>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-green-600">3. Оновлення токену</h3>
                                    <div class="bg-black text-green-400 p-3 rounded font-mono text-sm mt-2">
                                        <div>POST /api/auth/refresh</div>
                                        <div class="text-gray-300">Content-Type: application/json</div>
                                        <div class="mt-2">
                                            {<br>
                                            &nbsp;&nbsp;"refresh_token": "abc123def456..."<br>
                                            }
                                        </div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <strong>Відповідь:</strong><br>
                                        {<br>
                                        &nbsp;&nbsp;"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",<br>
                                        &nbsp;&nbsp;"refresh_token": "xyz789uvw012...",<br>
                                        &nbsp;&nbsp;"expires_in": 3600<br>
                                        }
                                    </div>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-green-600">4. Отримання інформації про користувача</h3>
                                    <div class="bg-black text-green-400 p-3 rounded font-mono text-sm mt-2">
                                        <div>GET /api/auth/me</div>
                                        <div class="text-gray-300">Authorization: Bearer {access_token}</div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <strong>Відповідь:</strong><br>
                                        {<br>
                                        &nbsp;&nbsp;"user": { "id": 1, "name": "Master 4567", "phone": "+380501234567" }<br>
                                        }
                                    </div>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-green-600">5. Вихід з системи</h3>
                                    <div class="bg-black text-green-400 p-3 rounded font-mono text-sm mt-2">
                                        <div>POST /api/auth/logout</div>
                                        <div class="text-gray-300">Authorization: Bearer {access_token}</div>
                                        <div class="text-gray-300">Content-Type: application/json</div>
                                        <div class="mt-2">
                                            {<br>
                                            &nbsp;&nbsp;"refresh_token": "abc123def456..."<br>
                                            }
                                        </div>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <strong>Відповідь:</strong> { "message": "Successfully logged out" }
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Token Management Section -->
                        <section>
                            <h2 class="text-xl font-semibold mb-4 text-blue-600">⏰ Управління токенами</h2>

                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-yellow-800 mb-2">Важлива інформація:</h3>
                                <ul class="text-sm text-yellow-700 space-y-1">
                                    <li>• <strong>Access Token</strong> дійсний 60 хвилин (1 година)</li>
                                    <li>• <strong>Refresh Token</strong> дійсний 14 днів</li>
                                    <li>• Зберігайте refresh_token локально в додатку</li>
                                    <li>• Автоматично оновлюйте access_token при закінченні</li>
                                    <li>• Перевіряйте авторизацію при запуску додатку</li>
                                </ul>
                            </div>
                        </section>

                        <!-- Implementation Guide Section -->
                        <section>
                            <h2 class="text-xl font-semibold mb-4 text-blue-600">📱 Інструкція для розробника</h2>

                            <div class="bg-blue-50 p-4 rounded-lg space-y-4">
                                <div>
                                    <h3 class="font-semibold text-blue-800">1. Збереження токенів</h3>
                                    <div class="bg-black text-blue-400 p-3 rounded font-mono text-sm mt-2">
                                        <div>// Flutter example</div>
                                        <div>class TokenStorage {</div>
                                        <div>&nbsp;&nbsp;static const String _accessTokenKey = 'access_token';</div>
                                        <div>&nbsp;&nbsp;static const String _refreshTokenKey = 'refresh_token';</div>
                                        <div>&nbsp;&nbsp;static const String _tokenExpiryKey = 'token_expiry';</div>
                                        <div>&nbsp;&nbsp;</div>
                                        <div>&nbsp;&nbsp;static Future&lt;void&gt; saveTokens(String accessToken, String refreshToken, DateTime expiry) async {</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;final prefs = await SharedPreferences.getInstance();</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;await prefs.setString(_accessTokenKey, accessToken);</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;await prefs.setString(_refreshTokenKey, refreshToken);</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;await prefs.setString(_tokenExpiryKey, expiry.toIso8601String());</div>
                                        <div>&nbsp;&nbsp;}</div>
                                        <div>}</div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-blue-800">2. Перевірка авторизації при старті</h3>
                                    <div class="bg-black text-blue-400 p-3 rounded font-mono text-sm mt-2">
                                        <div>class AuthService {</div>
                                        <div>&nbsp;&nbsp;Future&lt;bool&gt; checkAuthStatus() async {</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;final token = await TokenStorage.getAccessToken();</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;if (token == null) return false;</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;if (!await TokenStorage.isTokenValid()) {</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;return await refreshToken();</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;}</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;return true;</div>
                                        <div>&nbsp;&nbsp;}</div>
                                        <div>}</div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-blue-800">3. Автоматичне оновлення токену</h3>
                                    <div class="bg-black text-blue-400 p-3 rounded font-mono text-sm mt-2">
                                        <div>Future&lt;bool&gt; refreshToken() async {</div>
                                        <div>&nbsp;&nbsp;final refreshToken = await TokenStorage.getRefreshToken();</div>
                                        <div>&nbsp;&nbsp;if (refreshToken == null) return false;</div>
                                        <div>&nbsp;&nbsp;</div>
                                        <div>&nbsp;&nbsp;try {
                                        </div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;final response = await http.post(</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Uri.parse('$baseUrl/api/auth/refresh'),</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;headers: {'Content-Type': 'application/json'},</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;body: json.encode({'refresh_token': refreshToken}),</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;);</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;if (response.statusCode == 200) {</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;final data = json.decode(response.body);</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;await TokenStorage.saveTokens(</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;data['access_token'],</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;data['refresh_token'],</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DateTime.now().add(Duration(hours: 1)),</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;);</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;return true;</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;}</div>
                                        <div>&nbsp;&nbsp;} catch (e) {</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;print('Token refresh failed: $e');</div>
                                        <div>&nbsp;&nbsp;}</div>
                                        <div>&nbsp;&nbsp;return false;</div>
                                        <div>}</div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Error Handling Section -->
                        <section>
                            <h2 class="text-xl font-semibold mb-4 text-blue-600">⚠️ Обробка помилок</h2>

                            <div class="bg-red-50 p-4 rounded-lg space-y-3">
                                <div>
                                    <h3 class="font-semibold text-red-800">Коди помилок:</h3>
                                    <ul class="text-sm text-red-700 space-y-1">
                                        <li>• <strong>400</strong> - Невірний OTP код</li>
                                        <li>• <strong>401</strong> - Невірний або прострочений токен</li>
                                        <li>• <strong>422</strong> - Відсутній refresh_token</li>
                                        <li>• <strong>500</strong> - Внутрішня помилка сервера</li>
                                    </ul>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-red-800">Приклад обробки:</h3>
                                    <div class="bg-black text-red-400 p-3 rounded font-mono text-sm mt-2">
                                        <div>try {</div>
                                        <div>&nbsp;&nbsp;final response = await http.post(...);</div>
                                        <div>&nbsp;&nbsp;if (response.statusCode == 401) {</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;// Токен прострочений, спробувати оновити</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;final refreshed = await refreshToken();</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;if (!refreshed) {</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;// Перенаправити на екран входу</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;navigateToLogin();</div>
                                        <div>&nbsp;&nbsp;&nbsp;&nbsp;}</div>
                                        <div>&nbsp;&nbsp;}</div>
                                        <div>} catch (e) {</div>
                                        <div>&nbsp;&nbsp;print('Request failed: $e');</div>
                                        <div>}</div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Base URL Section -->
                        <section>
                            <h2 class="text-xl font-semibold mb-4 text-blue-600">🌐 Базовий URL</h2>

                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-sm text-green-700">
                                    <strong>Розробка:</strong> http://localhost:100<br>
                                    <strong>Продакшн:</strong> https://your-domain.com
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
</script>
