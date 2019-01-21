/* 
 * Copyright (C) 2017 arx iT
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * Deletes a process.
 *
 * @param {int}     id      The identifier of the process to delete
 * @param {string}  name    The name of the process to delete (only for display purposes)
 */
function deleteProcess(id, name) {

    if (!id || isNaN(id) || id <= 0 || !name) {
        return;
    }

    var deleteConfirmTexts = LANG_MESSAGES.processesList.deleteConfirm;
    var alertButtonsTexts = LANG_MESSAGES.generic.alertButtons;
    var message = deleteConfirmTexts.message.replace('\{0\}', name);
    var confirmedCallback = function() {
        $('#processId').val(id);
        $('#processName').val(name);
        $('#processForm').submit();
    };

    showConfirm(deleteConfirmTexts.title, message, confirmedCallback, null, alertButtonsTexts.yes,
            alertButtonsTexts.no);
}


/********************* EVENT HANDLERS *********************/

$(function() {
    $('.delete-button').on('click', function() {
        var $button = $(this);
        var id = parseInt($button.attr('id').replace('deleteButton-', ''));

        if (isNaN(id)) {
            return;
        }

        var name = $button.closest('tr').find('td.nameCell > a').text();

        if (!name) {
            return;
        }

        deleteProcess(id, name);
    });
});