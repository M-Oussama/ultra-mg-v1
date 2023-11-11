import { notification } from 'ant-design-vue';
import { useI18n } from 'vue-i18n';

export function errorsMiddleware(message,description) {

        notification.error({ message: message, description: description, duration:4 });

}
