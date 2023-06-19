class CF7EntryHandler {
    constructor() {
        this.events();
    }

    events() {
        document.querySelectorAll('.cf7-approve-button').forEach(button => {
            button.addEventListener('click', this.handleApprove.bind(this));
        });

        document.querySelectorAll('.cf7-decline-button').forEach(button => {
            button.addEventListener('click', this.handleDecline.bind(this));
        });
    }

    handleApprove(e) {
        e.preventDefault();
        this.handleEntryAction(e.target, 'cf7_approve_entry', 'Approved');
    }

    handleDecline(e) {
        e.preventDefault();
        this.handleEntryAction(e.target, 'cf7_refuse_entry', 'Declined');
    }

    handleEntryAction(button, action, statusText) {
        const entryId = button.closest('form').querySelector('input[name="cf7_entry_id"]').value;
        const tableRow = button.closest('tr');

        fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: action,
                cf7_entry_id: entryId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data) {
                    const statusCell = tableRow.querySelector('.status-cell');
                    const emailSentCell = tableRow.querySelector('.email-sent-cell');
                    statusCell.textContent = statusText;
                    emailSentCell.innerHTML = '✅';
                    tableRow.querySelector('.action-buttons').style.display = 'none';
                }
            })
            .catch(error => {
                console.log(error);
            });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CF7EntryHandler();
});
