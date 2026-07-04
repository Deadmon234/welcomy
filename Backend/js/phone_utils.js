/**
 * Gestion des numéros de téléphone internationaux (Welcomy).
 */
window.WELCOMY_PHONE = {
  countries: [
    { code: 'CM', name: 'Cameroun', dial: '+237', min: 9, max: 9, pattern: /^6\d{8}$/, placeholder: '6XX XXX XXX' },
    { code: 'FR', name: 'France', dial: '+33', min: 9, max: 9, pattern: /^[1-9]\d{8}$/, placeholder: '6 XX XX XX XX' },
    { code: 'CI', name: "Côte d'Ivoire", dial: '+225', min: 10, max: 10, pattern: /^\d{10}$/, placeholder: '07 XX XX XX XX' },
    { code: 'SN', name: 'Sénégal', dial: '+221', min: 9, max: 9, pattern: /^7\d{8}$/, placeholder: '7X XXX XX XX' },
    { code: 'GA', name: 'Gabon', dial: '+241', min: 8, max: 8, pattern: /^\d{8}$/, placeholder: '0X XX XX XX' },
    { code: 'CG', name: 'Congo', dial: '+242', min: 9, max: 9, pattern: /^\d{9}$/, placeholder: '0X XXX XX XX' },
    { code: 'CD', name: 'RD Congo', dial: '+243', min: 9, max: 9, pattern: /^\d{9}$/, placeholder: '8XX XXX XXX' },
    { code: 'NG', name: 'Nigeria', dial: '+234', min: 10, max: 10, pattern: /^\d{10}$/, placeholder: '80X XXX XXXX' },
    { code: 'BJ', name: 'Bénin', dial: '+229', min: 8, max: 10, pattern: /^\d{8,10}$/, placeholder: 'XX XX XX XX' },
    { code: 'TG', name: 'Togo', dial: '+228', min: 8, max: 8, pattern: /^\d{8}$/, placeholder: 'XX XX XX XX' },
    { code: 'BE', name: 'Belgique', dial: '+32', min: 9, max: 9, pattern: /^[1-9]\d{8}$/, placeholder: '4XX XX XX XX' },
    { code: 'CH', name: 'Suisse', dial: '+41', min: 9, max: 9, pattern: /^\d{9}$/, placeholder: '7X XXX XX XX' },
    { code: 'US', name: 'États-Unis', dial: '+1', min: 10, max: 10, pattern: /^\d{10}$/, placeholder: 'XXX XXX XXXX' },
  ],

  findByDial(dial) {
    return this.countries.find(c => c.dial === dial) || null;
  },

  findByCode(code) {
    return this.countries.find(c => c.code === code) || this.countries[0];
  },

  detectFromRaw(raw) {
    const digits = (raw || '').replace(/\D/g, '');
    if (!digits) return null;
    const sorted = [...this.countries].sort((a, b) => b.dial.length - a.dial.length);
    for (const c of sorted) {
      const code = c.dial.replace('+', '');
      if (digits.startsWith(code)) return c;
    }
    return null;
  },

  onlyDigits(v) {
    return (v || '').replace(/\D/g, '');
  },

  normalize(raw, dialCode) {
    const trimmed = (raw || '').trim();
    if (!trimmed) return { ok: false, error: 'Numéro requis.' };

    let country = null;
    let national = this.onlyDigits(trimmed);

    if (trimmed.startsWith('+') || (national.length > 10 && this.detectFromRaw(trimmed))) {
      country = this.detectFromRaw(trimmed);
      if (country) {
        national = national.slice(country.dial.replace('+', '').length);
      }
    } else if (dialCode) {
      country = this.findByDial(dialCode);
    }
    if (!country) country = this.findByDial(dialCode || '+237') || this.countries[0];

    national = national.replace(/^0+/, '');

    if (!country.pattern.test(national) || national.length < country.min || national.length > country.max) {
      return {
        ok: false,
        error: `Numéro invalide pour ${country.name}. Exemple : ${country.placeholder}`,
      };
    }

    return { ok: true, phone: country.dial + national, country, national };
  },

  /** Initialise un bloc [data-phone-field] */
  initField(root) {
    if (!root || root.dataset.phoneReady) return root;
    const countrySel = root.querySelector('[data-phone-country]');
    const numberIn = root.querySelector('[data-phone-number]');
    const hiddenIn = root.querySelector('[data-phone-full]');
    const dialHidden = root.querySelector('[data-phone-dial-sync]');
    const hintEl = root.querySelector('[data-phone-hint]');
    if (!countrySel || !numberIn || !hiddenIn) return root;

    countrySel.innerHTML = this.countries.map(c =>
      `<option value="${c.dial}">${c.name} (${c.dial})</option>`
    ).join('');

    const defaultDial = root.dataset.defaultCountry || '+237';
    countrySel.value = this.findByDial(defaultDial) ? defaultDial : '+237';

    const update = () => {
      const country = this.findByDial(countrySel.value);
      if (dialHidden) dialHidden.value = countrySel.value;
      numberIn.placeholder = country?.placeholder || 'Numéro';
      const result = this.normalize(numberIn.value, countrySel.value);
      if (result.ok) {
        hiddenIn.value = result.phone;
        if (hintEl) {
          hintEl.textContent = `Enregistré : ${result.phone}`;
          hintEl.className = 'text-xs text-emerald-400/90 mt-1.5';
        }
      } else {
        hiddenIn.value = '';
        if (hintEl && numberIn.value.trim()) {
          hintEl.textContent = result.error;
          hintEl.className = 'text-xs text-amber-400 mt-1.5';
        } else if (hintEl) {
          hintEl.textContent = `Indicatif ${countrySel.value} — saisissez le numéro sans l'indicatif`;
          hintEl.className = 'text-xs text-slate-500 mt-1.5';
        }
      }
    };

    const onNumberInput = () => {
      const v = numberIn.value.trim();
      if (v.startsWith('+') || v.replace(/\D/g, '').length > 10) {
        const detected = this.detectFromRaw(v);
        if (detected) {
          countrySel.value = detected.dial;
          const codeLen = detected.dial.replace('+', '').length;
          numberIn.value = v.replace(/\D/g, '').slice(codeLen);
        }
      }
      update();
    };

    countrySel.addEventListener('change', update);
    numberIn.addEventListener('input', onNumberInput);
    update();

    root.validate = () => {
      const result = this.normalize(numberIn.value, countrySel.value);
      if (!result.ok) return result;
      hiddenIn.value = result.phone;
      return result;
    };

    root.dataset.phoneReady = '1';
    return root;
  },

  initAll(selector = '[data-phone-field]') {
    document.querySelectorAll(selector).forEach(el => this.initField(el));
  },
};
