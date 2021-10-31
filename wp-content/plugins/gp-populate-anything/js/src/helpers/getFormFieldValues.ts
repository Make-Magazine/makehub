const $ = window.jQuery;

/**
 * serializeArray() does not include disabled inputs by default.
 *
 * @credit https://stackoverflow.com/q/15958671
 */
function serializeAll($form: JQuery) {
	var data = $form.serializeArray();

	$(':disabled[name]', $form).each((index, el) => {
		if (!(el as HTMLInputElement).name) {
			return;
		}

		data.push({ name: (el as HTMLInputElement).name, value: $(el).val() });
	});

	return data;
}

export default function getFormFieldValues(formId?:number|string, isGravityView:boolean = false) {

	var $form = $('#gform_' + formId);
	var inputPrefix = 'input_';

	if (isGravityView) {
		inputPrefix = 'filter_';
	}

	/* Use entry form if we're in the Gravity Forms admin entry view. */
	if ( $('#wpwrap #entry_form').length ) {
		$form = $('#entry_form');
	}

	if ( isGravityView ) {
		$form = $( '.gv-widget-search' );
	}

	var inputsArray = $.grep(serializeAll($form), function (value?: { name: string, value: string }) {
		if (!value || value.name.indexOf(inputPrefix) !== 0) {
			return false;
		}

		return true;
	});

	var inputsObject: { [input: string]: string[] | string } = {};

	for (const input of inputsArray) {
		const { value } = input;
		const $input = $form.find(`[name="${input.name}"]`);
		let inputName = input.name.replace(inputPrefix, '');

		/**
		 * Do not send input value if it is not checked otherwise when hydrating values, it will be treated as if it
		 * was checked.
		 */
		if ($input.is(':checkbox') && !$input.is(':checked')) {
			continue;
		}

		/* Handle array-based inputs such as the Time field */
		if (inputName.indexOf('[]') !== -1) {
			inputName = inputName.replace('[]', '');

			if (!(inputName in inputsObject)) {
				inputsObject[inputName] = [];
			}

			(inputsObject[inputName] as string[]).push(value);
			/* Standard inputs */
		} else {
			inputsObject[inputName] = value;
		}
	}

	return inputsObject;

}
