import { notification } from 'ant-design-vue';
import { useI18n } from 'vue-i18n';

export function errorsMiddleware(action) {
    const { t } = useI18n(); // Access the $t function
    if (action.status !== 401) {
        notification.error({ message: action.status + ' - ' + action.data.message });
    }

    if (action.status === 503) {
        window.location.href = '/maintenance';
    }
}
