import 'jquery'; // Jawny import, nawet z ProvidePlugin, może pomóc w kolejności
import 'bootstrap'; // Import Bootstrap JS
import 'jquery-ui';
import 'jquery-confirm'; // Import jQuery Confirm
import 'bootstrap-table';
import 'bootstrap-table-locale';
import 'bootstrap-table-cookie';
import 'bootstrap-table-filter-control';
import 'bootstrap-table-sticky-header';
import 'bootstrap-table-toolbar';
import 'bootstrap-table-auto-refresh';
import 'bootstrap-table-page-jump-to';
import 'bootstrap-table-custom-view';
import 'bootstrap-table-mobile';
import 'bootstrap-table-multiple-sort';
import 'bootstrap-table-addrbar';
import 'xeditable'; // Import x-editable
import 'bootstrap-table-editable';
import 'table-dnd';
import 'bootstrap-table-reorder-rows';
import { Fancybox } from "fancybox";
/*
import 'bootstrap-table-print';
import 'bootstrap-table-export';
*/

Fancybox.bind("[data-fancybox]", {});

const showMessage = (message, isSuccess = true, duration = 5000) => {
	const $alertContainer = $('.message'); // Kontener (.message) - to on ma .show/.hide
	const $alert = $alertContainer.find('.alert'); // Sam element komunikatu (.alert)

	// 1. ZATRZYMAJ wszelkie trwające animacje i usuń style inline
	$alertContainer.stop(true, true).css('display', '');
	$alert.stop(true, true).css('display', '');

	// 2. Wypełnij treść i ustaw klasy
	$alert
		.html(message)
		.removeClass('alert-success alert-danger')
		.addClass(isSuccess ? 'alert-success' : 'alert-danger');

	// 3. POKAŻ kontener (jeśli jest ukryty po poprzednim fadeOut)
	$alertContainer.show(500);

	// 4. UKRYJ po upływie czasu
	// Używamy .delay() i .fadeOut().
	// Ważne: fadeOut na końcu ukrywa element (display: none).
	$alertContainer
		.delay(duration)
		.fadeOut('slow'); // fadeOut jest na kontenerze
};

// Funkcja pomocnicza do obsługi odpowiedzi AJAX
const handleAjaxResponse = (response, $table, url, form, reset) => {    //response, $table, tableUrl, $form
	if (response.success) {
		showMessage(response.message);

		if ($table) {
			$table.bootstrapTable('refresh', { url });
		}

		if (reset === true) {
			form.trigger('reset');
		}

	} else {
		const errorMessage = response.errors ? response.errors.join('<br>') : 'Coś poszło nie tak!';
		showMessage(errorMessage, false);
	}
};

// Funkcja pomocnicza do obsługi błędów AJAX
const handleAjaxError = (jqXHR) => {
	let errorMsg = 'Coś poszło nie tak!';
	if (jqXHR.responseJSON) {
		errorMsg = jqXHR.responseJSON.errors?.join('<br>') || jqXHR.responseJSON.error || errorMsg;
	}
	showMessage(errorMsg, false);
};

// Inicjalizacja tabeli
const initTable = ($table, url, columns) => {
	$table.bootstrapTable({
		url,
		search: true,
		pagination: true,
		buttonsClass: 'primary',
		showFooter: false,
		minimumCountColumns: 2,
		columns,
	});
};

// Usuwanie rekordów
const initDelete = ($table, $removeButton, url, idField, confirmMessage) => {
	const getIdSelections = () =>
		$.map($table.bootstrapTable('getSelections'), (row) => row[idField]);

	$removeButton.on('click', () => {
		const ids = getIdSelections();
		if (!ids.length) return;

		$.confirm({
			title: 'Usuwanie!',
			content: confirmMessage,
			buttons: {
				confirm: {
					text: 'Tak',
					action: () => {
						ids.forEach((id) => {
							$.ajax({
								url,
								data: { id },
								type: 'POST',
								dataType: 'json',
								cache: false,
								success: (response) => handleAjaxResponse(response, $table, $table.bootstrapTable('getOptions').url),
								error: handleAjaxError,
							});
						});
						$table.bootstrapTable('remove', { field: idField, values: ids });
						$removeButton.prop('disabled', true);
					},
				},
				cancel: { text: 'Anuluj' },
			},
		});
	});

	$table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', () => {
		$removeButton.prop('disabled', !getIdSelections().length);
	});
};

// Inicjalizacja formularza
const initForm = ($form, url, $table, tableUrl, reset) => {
	$form.on('submit', (e) => {
		e.preventDefault();
		const formData = new FormData($form[0]);

		$.ajax({
			url,
			type: 'POST',
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			dataType: 'json',
			success: (response) => handleAjaxResponse(response, $table, tableUrl, $form, reset),
			error: handleAjaxError,
		});
	});
};

/* Navigation,Cloack && Navbar */
function initNavigationAndClock() {
	// Inicjalizacja nawigacji
	function setupNavbar() {
		const toggle = document.querySelector('#header-toggle');
		const navbar = document.querySelector('#nav-bar');
		const bodypd = document.querySelector('#body-pd');
		const headerpd = document.querySelector('#header');
		const navNames = document.querySelectorAll('.nav_name');

		// Walidacja elementów
		if (!toggle || !navbar || !bodypd || !headerpd) {
			console.error('Brak wymaganych elementów nawigacji w DOM');
			return;
		}

		toggle.addEventListener('click', () => {
			// Przełączanie klas
			navbar.classList.toggle('show');
			toggle.classList.toggle('bx-x');
			bodypd.classList.toggle('body-pd');
			headerpd.classList.toggle('body-pd');

			// Przełączanie widoczności .nav_name
			navNames.forEach((el) => {
				el.style.display = el.style.display === 'none' ? 'block' : 'none';
			});
		});

		// Obsługa aktywnych linków
		const linkColors = document.querySelectorAll('.nav_link');
		linkColors.forEach((link) => {
			link.addEventListener('click', () => {
				linkColors.forEach((l) => l.classList.remove('active'));
				link.classList.add('active');
			});
		});
	}

	// Inicjalizacja zegara
	function startClock() {
		const clockElement = document.querySelector('#clock');
		if (!clockElement) {
			console.error('Brak elementu #clock w DOM');
			return;
		}

		function updateClock() {
			const now = new Date();
			clockElement.textContent = now.toLocaleTimeString('pl-PL', {
				hour: '2-digit',
				minute: '2-digit',
				second: '2-digit',
				hour12: false,
			});
			setTimeout(updateClock, 1000);
		}

		updateClock();
	}

	setupNavbar();
	startClock();

	$('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
		localStorage.setItem('activeTab', $(e.target).attr('href'));
	});

	var activeTab = localStorage.getItem('activeTab');
	if (activeTab) {
		$('.nav-tabs a[href="' + activeTab + '"]').tab('show');
	}
}

// Uruchomienie inicjalizacji po załadowaniu DOM
document.addEventListener('DOMContentLoaded', initNavigationAndClock);

/**
 * Dynamiczne ładowanie modułu JS na podstawie nazwy kontrolera
 * pobranej z bazy danych przez AJAX.
 */
function loadModuleFromRoute() {
	var path = window.location.pathname;

	// Używamy pełnej ścieżki (path) do wysłania zapytania do API
	var apiUrl = '/api/get-controller-by-url';

	fetch(apiUrl, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			// Pamiętaj, aby dodać nagłówek CSRF, jeśli jest wymagany przez SimpleRouter
			// np. 'X-Csrf-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
		},
		body: JSON.stringify({
			url: path // Wysyłamy pełną ścieżkę URL, którą PeceeSimpleRouter przetwarza
		})
	})
		.then(response => {
			// Sprawdź, czy odpowiedź jest w porządku (np. status 200)
			if (!response.ok) {
				throw new Error('Network response was not ok, status: ' + response.status);
			}
			return response.json();
		})
		.then(data => {
			// Sprawdź, czy serwer zwrócił ControllerName
			if (data.controller_name) {
				var ControllerName = data.controller_name; // Nazwa Controlera Np. 'UsersController'
				var ActionName = data.action_name; // Akcja
				console.log(ActionName);

				// Usuwamy 'Controller' z nazwy, aby pasowała do struktury katalogów 'app/Views/Users'
				// Zakładamy, że ControllerName to np. 'UsersController', a katalog to 'Users'
				var viewFolderName = ControllerName.replace(/Controller$/, '');

				if (ActionName == 'index') {
					// Sprawdź, czy moduł istnieje
					import(`../../app/Views/${viewFolderName}/index.js`)
						.then(module => {
							// Sprawdź, czy moduł eksportuje funkcję initModule
							if (typeof module.initModule === 'function') {
								module.initModule(); // Wywołaj funkcję inicjalizującą
							} else {
								console.warn(`Moduł '${viewFolderName}' nie eksportuje funkcji 'initModule'.`);
							}
						})
						.catch(error => {
							console.error(`Nie udało się załadować modułu widoku dla '${viewFolderName}':`, error);
						});
				}

				if (ActionName == 'edit') {
					// Sprawdź, czy moduł istnieje
					import(`../../app/Views/${viewFolderName}/edit.js`)
						.then(module => {
							// Sprawdź, czy moduł eksportuje funkcję initModule
							if (typeof module.initModule === 'function') {
								module.initModule(); // Wywołaj funkcję inicjalizującą
							} else {
								console.warn(`Moduł '${viewFolderName}' nie eksportuje funkcji 'initModule'.`);
							}
						})
						.catch(error => {
							console.error(`Nie udało się załadować modułu widoku dla '${viewFolderName}':`, error);
						});
				}

			} else {
				console.warn('Serwer nie zwrócił nazwy kontrolera dla tego URL.');
			}
		})
		.catch(error => {
			// Obsługa błędów (np. 404 Route not found, błędy sieci)
			console.error('Błąd pobierania nazwy kontrolera przez AJAX:', error);
		});
}

export {
	initTable,
	initDelete,
	initForm,
	showMessage,
	handleAjaxResponse,
	handleAjaxError,
	// Dodaj inne, jeśli potrzebne, np. loadModuleFromRoute jeśli chcesz je używać gdzie indziej
};
// Wywołaj funkcję, aby rozpocząć proces
loadModuleFromRoute();