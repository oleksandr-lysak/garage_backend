import { createI18n } from 'vue-i18n'

// Import language files
import uk from '../lang/uk.json'
import en from '../lang/en.json'

declare global {
  interface Window {
    translations?: {
      locale?: string;
      messages?: Record<string, any>;
    };
  }
}

const locale = window?.translations?.locale || 'uk' // Default to Ukrainian
const messages = {
  uk,
  en,
  ...(window?.translations?.messages ? { [locale]: window.translations.messages } : {})
}

const i18n = createI18n({
  legacy: false,
  locale,
  fallbackLocale: 'uk',
  messages,
})

export default i18n
