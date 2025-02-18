import { DynamicDialogConfig } from 'primeng/dynamicdialog';

export const defaultDialogConfig: Partial<DynamicDialogConfig> = {
    modal: true,
    closeOnEscape: true,
    dismissableMask: true,
    styleClass: 'p-dialog-no-padding'
};
