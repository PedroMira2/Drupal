/**
 * @file
 * Webform Booking translations handler.
 */

(function (Drupal) {
  'use strict';

  /**
   * Global translation function for webform booking.
   *
   * @param {string} key - The translation key.
   * @param {string} fallback - Fallback text if translation not found.
   * @returns {string} The translated text.
   */
  window.webformBookingT = function(key, fallback = '') {
    if (typeof drupalSettings !== 'undefined' &&
        drupalSettings.webform_booking &&
        drupalSettings.webform_booking.translations &&
        drupalSettings.webform_booking.translations[key]) {
      return drupalSettings.webform_booking.translations[key];
    }
    return fallback || key;
  };

  /**
   * Get weekday translations array.
   *
   * @returns {Array} Array of translated weekday names.
   */
  window.webformBookingGetWeekdays = function() {
    return [
      window.webformBookingT('monday_short', 'Mon'),
      window.webformBookingT('tuesday_short', 'Tue'),
      window.webformBookingT('wednesday_short', 'Wed'),
      window.webformBookingT('thursday_short', 'Thu'),
      window.webformBookingT('friday_short', 'Fri'),
      window.webformBookingT('saturday_short', 'Sat'),
      window.webformBookingT('sunday_short', 'Sun')
    ];
  };

  /**
   * Get translated month name using browser's Intl API.
   *
   * @param {number} monthIndex - Month index (0-11).
   * @returns {string} Translated month name.
   */
  window.webformBookingGetMonthName = function(monthIndex) {
    // Create a date object for the first day of the given month
    const date = new Date(2000, monthIndex, 1);

    // Get current language from Drupal
    let language = window.webformBookingGetLanguage();

    // Map language codes to locale strings that JavaScript understands
    const localeMap = {
      'pt': 'pt-PT',
      'pt-br': 'pt-BR',
      'zh': 'zh-CN',
      'zh-cn': 'zh-CN',
      'zh-tw': 'zh-TW'
    };

    const locale = localeMap[language] || language || 'en';

    try {
      // Use the browser's Intl API with the appropriate locale
      return date.toLocaleDateString(locale, { month: 'long' });
    } catch (e) {
      // Fallback to English if locale is not supported
      return date.toLocaleDateString('en', { month: 'long' });
    }
  };

    /**
   * Get current language code from settings.
   *
   * @returns {string} The language code.
   */
  window.webformBookingGetLanguage = function() {
    if (typeof drupalSettings !== 'undefined' &&
        drupalSettings.webform_booking &&
        drupalSettings.webform_booking.currentLanguage) {
      return drupalSettings.webform_booking.currentLanguage;
    }
    return 'en';
  };

  /**
   * Get current country code from settings (for backward compatibility).
   *
   * @returns {string} The country code.
   */
  window.webformBookingGetCountry = function() {
    if (typeof drupalSettings !== 'undefined' &&
        drupalSettings.webform_booking &&
        drupalSettings.webform_booking.defaultCountry) {
      return drupalSettings.webform_booking.defaultCountry;
    }
    return 'GB';
  };

  /**
   * Check if we're in a right-to-left language context.
   *
   * @returns {boolean} True if RTL, false otherwise.
   */
  window.webformBookingIsRTL = function() {
    const language = window.webformBookingGetLanguage();
    const rtlLanguages = ['ar', 'he', 'fa', 'ur'];
    return rtlLanguages.includes(language);
  };

})(Drupal);
