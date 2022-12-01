import Polyglot from 'node-polyglot';
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
			'Please select one or more tickets': oeVars.strings.validator.select_tickets,
		})
		const validator = new FormValidator(registrationForm, polyglot);

		const ticketElements = registrationForm.querySelectorAll('[name*="ticket"]');
		ticketElements.forEach(element => {
			validator.addErrorFunction(element, function(errors, input){
				let ticketsWereChosen = false;
				ticketElements.forEach(ticket => {
					if (ticket.value > 0) {
						ticketsWereChosen = true;
					}
				});

				if (!ticketsWereChosen) {
					errors.required = input.polyglot.t('Please select one or more tickets');
				}

				return errors;
			});
		});
	}
});
