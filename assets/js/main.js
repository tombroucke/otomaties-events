import Polyglot from 'node-polyglot';
import '../scss/main.scss';
import FormValidator from '@tombroucke/otomaties-form-validator';
/* global oeVars:true */

window.addEventListener('DOMContentLoaded', (event) => {
	const registrationForm = document.querySelector('.js-form-event-registration');
	if (registrationForm) {
		var polyglot = new Polyglot();
		polyglot.extend({
			'This field is required': oeVars.strings.validator.required,
			'Enter a value less than or equal to {0}': oeVars.strings.validator.maxValue,
			'Enter a value greater than or equal to {0}': oeVars.strings.validator.minValue,
			'Please enter a valid e-mailaddress': oeVars.strings.validator.email,
		})
		new FormValidator(registrationForm, polyglot);
	}
});
