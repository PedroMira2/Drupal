<?php

namespace Drupal\webform_booking\Service;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Service for webform booking translations.
 */
class WebformBookingTranslations {

  use StringTranslationTrait;

    /**
   * Get all translations for a specific language.
   *
   * @param string $language_code
   *   The language code (e.g., 'en', 'fr', 'es').
   *
   * @return array
   *   Array of translations keyed by translation key.
   */
  public function getTranslations($language_code = 'en') {
    $translations = $this->getBaseTranslations();

    // Override with language-specific translations if available.
    $language_translations = $this->getLanguageSpecificTranslations($language_code);

    return array_merge($translations, $language_translations);
  }

  /**
   * Get base English translations.
   *
   * @return array
   *   Array of base translations.
   */
  protected function getBaseTranslations() {
    return [
      // Calendar navigation.
      'previous_month' => $this->t('Previous month'),
      'next_month' => $this->t('Next month'),
      'today' => $this->t('Today'),

      // Weekdays (short form).
      'monday_short' => $this->t('Mon'),
      'tuesday_short' => $this->t('Tue'),
      'wednesday_short' => $this->t('Wed'),
      'thursday_short' => $this->t('Thu'),
      'friday_short' => $this->t('Fri'),
      'saturday_short' => $this->t('Sat'),
      'sunday_short' => $this->t('Sun'),

      // Full weekdays.
      'monday' => $this->t('Monday'),
      'tuesday' => $this->t('Tuesday'),
      'wednesday' => $this->t('Wednesday'),
      'thursday' => $this->t('Thursday'),
      'friday' => $this->t('Friday'),
      'saturday' => $this->t('Saturday'),
      'sunday' => $this->t('Sunday'),

      // Months.
      'january' => $this->t('January'),
      'february' => $this->t('February'),
      'march' => $this->t('March'),
      'april' => $this->t('April'),
      'may' => $this->t('May'),
      'june' => $this->t('June'),
      'july' => $this->t('July'),
      'august' => $this->t('August'),
      'september' => $this->t('September'),
      'october' => $this->t('October'),
      'november' => $this->t('November'),
      'december' => $this->t('December'),

      // Booking interface.
      'no_slots_available' => $this->t('No slots available'),
      'select_date' => $this->t('Select a date'),
      'select_time' => $this->t('Select a time'),
      'select_seats' => $this->t('Select number of seats'),
      'price_label' => $this->t('Price'),
      'total_price_label' => $this->t('Total Price'),
      'subtotal_label' => $this->t('Subtotal'),
      'seats_label' => $this->t('Seats'),
      'quantity_label' => $this->t('Quantity'),
      'per_person' => $this->t('per person'),
      'available' => $this->t('Available'),
      'unavailable' => $this->t('Unavailable'),
      'selected' => $this->t('Selected'),
      'loading' => $this->t('Loading...'),
      'error_loading' => $this->t('Error loading'),
      'book_now' => $this->t('Book Now'),
      'cancel_booking' => $this->t('Cancel Booking'),
    ];
  }

  /**
   * Get language-specific translations.
   *
   * @param string $language_code
   *   The language code.
   *
   * @return array
   *   Array of language-specific translations.
   */
  protected function getLanguageSpecificTranslations($language_code) {
    $translations = [];

    switch ($language_code) {
      case 'fr':
        $translations = [
          'no_slots_available' => $this->t('Aucun créneau disponible'),
          'select_date' => $this->t('Sélectionner une date'),
          'select_time' => $this->t('Sélectionner une heure'),
          'select_seats' => $this->t('Sélectionner le nombre de places'),
          'price_label' => $this->t('Prix'),
          'total_price_label' => $this->t('Prix total'),
          'subtotal_label' => $this->t('Sous-total'),
          'seats_label' => $this->t('Places'),
          'quantity_label' => $this->t('Quantité'),
          'per_person' => $this->t('par personne'),
          'available' => $this->t('Disponible'),
          'unavailable' => $this->t('Indisponible'),
          'selected' => $this->t('Sélectionné'),
          'loading' => $this->t('Chargement...'),
          'error_loading' => $this->t('Erreur de chargement'),
          'book_now' => $this->t('Réserver maintenant'),
          'cancel_booking' => $this->t('Annuler la réservation'),
        ];
        break;

      case 'es':
        $translations = [
          'no_slots_available' => $this->t('No hay horarios disponibles'),
          'select_date' => $this->t('Seleccionar una fecha'),
          'select_time' => $this->t('Seleccionar una hora'),
          'select_seats' => $this->t('Seleccionar número de asientos'),
          'price_label' => $this->t('Precio'),
          'total_price_label' => $this->t('Precio total'),
          'subtotal_label' => $this->t('Subtotal'),
          'seats_label' => $this->t('Asientos'),
          'quantity_label' => $this->t('Cantidad'),
          'per_person' => $this->t('por persona'),
          'available' => $this->t('Disponible'),
          'unavailable' => $this->t('No disponible'),
          'selected' => $this->t('Seleccionado'),
          'loading' => $this->t('Cargando...'),
          'error_loading' => $this->t('Error al cargar'),
          'book_now' => $this->t('Reservar ahora'),
          'cancel_booking' => $this->t('Cancelar reserva'),
        ];
        break;

      case 'de':
        $translations = [
          'no_slots_available' => $this->t('Keine Termine verfügbar'),
          'select_date' => $this->t('Datum auswählen'),
          'select_time' => $this->t('Zeit auswählen'),
          'select_seats' => $this->t('Anzahl der Plätze auswählen'),
          'price_label' => $this->t('Preis'),
          'total_price_label' => $this->t('Gesamtpreis'),
          'subtotal_label' => $this->t('Zwischensumme'),
          'seats_label' => $this->t('Plätze'),
          'quantity_label' => $this->t('Menge'),
          'per_person' => $this->t('pro Person'),
          'available' => $this->t('Verfügbar'),
          'unavailable' => $this->t('Nicht verfügbar'),
          'selected' => $this->t('Ausgewählt'),
          'loading' => $this->t('Lädt...'),
          'error_loading' => $this->t('Ladefehler'),
          'book_now' => $this->t('Jetzt buchen'),
          'cancel_booking' => $this->t('Buchung stornieren'),
        ];
        break;

      case 'it':
        $translations = [
          'no_slots_available' => $this->t('Nessun orario disponibile'),
          'select_date' => $this->t('Seleziona una data'),
          'select_time' => $this->t('Seleziona un orario'),
          'select_seats' => $this->t('Seleziona numero di posti'),
          'price_label' => $this->t('Prezzo'),
          'total_price_label' => $this->t('Prezzo totale'),
          'subtotal_label' => $this->t('Subtotale'),
          'seats_label' => $this->t('Posti'),
          'quantity_label' => $this->t('Quantità'),
          'per_person' => $this->t('per persona'),
          'available' => $this->t('Disponibile'),
          'unavailable' => $this->t('Non disponibile'),
          'selected' => $this->t('Selezionato'),
          'loading' => $this->t('Caricamento...'),
          'error_loading' => $this->t('Errore di caricamento'),
          'book_now' => $this->t('Prenota ora'),
          'cancel_booking' => $this->t('Cancella prenotazione'),
        ];
        break;

      case 'nl':
        $translations = [
          'no_slots_available' => $this->t('Geen tijdsloten beschikbaar'),
          'select_date' => $this->t('Selecteer een datum'),
          'select_time' => $this->t('Selecteer een tijd'),
          'select_seats' => $this->t('Selecteer aantal stoelen'),
          'price_label' => $this->t('Prijs'),
          'total_price_label' => $this->t('Totaalprijs'),
          'subtotal_label' => $this->t('Subtotaal'),
          'seats_label' => $this->t('Stoelen'),
          'quantity_label' => $this->t('Hoeveelheid'),
          'per_person' => $this->t('per persoon'),
          'available' => $this->t('Beschikbaar'),
          'unavailable' => $this->t('Niet beschikbaar'),
          'selected' => $this->t('Geselecteerd'),
          'loading' => $this->t('Laden...'),
          'error_loading' => $this->t('Fout bij laden'),
          'book_now' => $this->t('Nu boeken'),
          'cancel_booking' => $this->t('Boeking annuleren'),
        ];
        break;

      case 'pt':
        $translations = [
          'no_slots_available' => $this->t('Nenhum horário disponível'),
          'select_date' => $this->t('Selecionar uma data'),
          'select_time' => $this->t('Selecionar um horário'),
          'select_seats' => $this->t('Selecionar número de lugares'),
          'price_label' => $this->t('Preço'),
          'total_price_label' => $this->t('Preço total'),
          'subtotal_label' => $this->t('Subtotal'),
          'seats_label' => $this->t('Lugares'),
          'quantity_label' => $this->t('Quantidade'),
          'per_person' => $this->t('por pessoa'),
          'available' => $this->t('Disponível'),
          'unavailable' => $this->t('Indisponível'),
          'selected' => $this->t('Selecionado'),
          'loading' => $this->t('Carregando...'),
          'error_loading' => $this->t('Erro ao carregar'),
          'book_now' => $this->t('Reservar agora'),
          'cancel_booking' => $this->t('Cancelar reserva'),
        ];
        break;

      case 'pl':
        $translations = [
          'no_slots_available' => $this->t('Brak dostępnych terminów'),
          'select_date' => $this->t('Wybierz datę'),
          'select_time' => $this->t('Wybierz godzinę'),
          'select_seats' => $this->t('Wybierz liczbę miejsc'),
          'price_label' => $this->t('Cena'),
          'total_price_label' => $this->t('Cena całkowita'),
          'subtotal_label' => $this->t('Suma częściowa'),
          'seats_label' => $this->t('Miejsca'),
          'quantity_label' => $this->t('Ilość'),
          'per_person' => $this->t('za osobę'),
          'available' => $this->t('Dostępne'),
          'unavailable' => $this->t('Niedostępne'),
          'selected' => $this->t('Wybrane'),
          'loading' => $this->t('Ładowanie...'),
          'error_loading' => $this->t('Błąd ładowania'),
          'book_now' => $this->t('Zarezerwuj teraz'),
          'cancel_booking' => $this->t('Anuluj rezerwację'),
        ];
        break;

      case 'ru':
        $translations = [
          'no_slots_available' => $this->t('Нет доступных слотов'),
          'select_date' => $this->t('Выберите дату'),
          'select_time' => $this->t('Выберите время'),
          'select_seats' => $this->t('Выберите количество мест'),
          'price_label' => $this->t('Цена'),
          'total_price_label' => $this->t('Общая цена'),
          'subtotal_label' => $this->t('Промежуточная сумма'),
          'seats_label' => $this->t('Места'),
          'quantity_label' => $this->t('Количество'),
          'per_person' => $this->t('на человека'),
          'available' => $this->t('Доступно'),
          'unavailable' => $this->t('Недоступно'),
          'selected' => $this->t('Выбрано'),
          'loading' => $this->t('Загрузка...'),
          'error_loading' => $this->t('Ошибка загрузки'),
          'book_now' => $this->t('Забронировать сейчас'),
          'cancel_booking' => $this->t('Отменить бронирование'),
        ];
        break;

      case 'ja':
        $translations = [
          'no_slots_available' => $this->t('利用可能なスロットがありません'),
          'select_date' => $this->t('日付を選択'),
          'select_time' => $this->t('時間を選択'),
          'select_seats' => $this->t('席数を選択'),
          'price_label' => $this->t('価格'),
          'total_price_label' => $this->t('合計価格'),
          'subtotal_label' => $this->t('小計'),
          'seats_label' => $this->t('席'),
          'quantity_label' => $this->t('数量'),
          'per_person' => $this->t('一人当たり'),
          'available' => $this->t('利用可能'),
          'unavailable' => $this->t('利用不可'),
          'selected' => $this->t('選択済み'),
          'loading' => $this->t('読み込み中...'),
          'error_loading' => $this->t('読み込みエラー'),
          'book_now' => $this->t('今すぐ予約'),
          'cancel_booking' => $this->t('予約をキャンセル'),
        ];
        break;

      case 'zh':
      case 'zh-cn':
        $translations = [
          'no_slots_available' => $this->t('没有可用的时段'),
          'select_date' => $this->t('选择日期'),
          'select_time' => $this->t('选择时间'),
          'select_seats' => $this->t('选择座位数'),
          'price_label' => $this->t('价格'),
          'total_price_label' => $this->t('总价'),
          'subtotal_label' => $this->t('小计'),
          'seats_label' => $this->t('座位'),
          'quantity_label' => $this->t('数量'),
          'per_person' => $this->t('每人'),
          'available' => $this->t('可用'),
          'unavailable' => $this->t('不可用'),
          'selected' => $this->t('已选择'),
          'loading' => $this->t('加载中...'),
          'error_loading' => $this->t('加载错误'),
          'book_now' => $this->t('立即预订'),
          'cancel_booking' => $this->t('取消预订'),
        ];
        break;

      case 'ko':
        $translations = [
          'no_slots_available' => $this->t('이용 가능한 슬롯이 없습니다'),
          'select_date' => $this->t('날짜 선택'),
          'select_time' => $this->t('시간 선택'),
          'select_seats' => $this->t('좌석 수 선택'),
          'price_label' => $this->t('가격'),
          'total_price_label' => $this->t('총 가격'),
          'subtotal_label' => $this->t('소계'),
          'seats_label' => $this->t('좌석'),
          'quantity_label' => $this->t('수량'),
          'per_person' => $this->t('인당'),
          'available' => $this->t('이용 가능'),
          'unavailable' => $this->t('이용 불가'),
          'selected' => $this->t('선택됨'),
          'loading' => $this->t('로딩 중...'),
          'error_loading' => $this->t('로딩 오류'),
          'book_now' => $this->t('지금 예약'),
          'cancel_booking' => $this->t('예약 취소'),
        ];
        break;

      case 'ar':
        $translations = [
          'no_slots_available' => $this->t('لا توجد فترات متاحة'),
          'select_date' => $this->t('اختر التاريخ'),
          'select_time' => $this->t('اختر الوقت'),
          'select_seats' => $this->t('اختر عدد المقاعد'),
          'price_label' => $this->t('السعر'),
          'total_price_label' => $this->t('السعر الإجمالي'),
          'subtotal_label' => $this->t('المجموع الفرعي'),
          'seats_label' => $this->t('المقاعد'),
          'quantity_label' => $this->t('الكمية'),
          'per_person' => $this->t('للشخص الواحد'),
          'available' => $this->t('متاح'),
          'unavailable' => $this->t('غير متاح'),
          'selected' => $this->t('محدد'),
          'loading' => $this->t('جاري التحميل...'),
          'error_loading' => $this->t('خطأ في التحميل'),
          'book_now' => $this->t('احجز الآن'),
          'cancel_booking' => $this->t('إلغاء الحجز'),
        ];
        break;
    }

    return $translations;
  }

  /**
   * Get JavaScript-ready translations for a specific language.
   *
   * @param string $language_code
   *   The language code.
   *
   * @return array
   *   Array of translations ready for JavaScript consumption.
   */
  public function getJavaScriptTranslations($language_code = 'en') {
    $translations = $this->getTranslations($language_code);

    // Convert translatable objects to strings for JavaScript
    // Convert translatable objects to strings for JavaScript.
    $js_translations = [];
    foreach ($translations as $key => $translation) {
      $js_translations[$key] = (string) $translation;
    }

    return $js_translations;
  }

}
