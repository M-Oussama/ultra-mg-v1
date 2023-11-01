import {notification} from "ant-design-vue";

export function successMiddleware(message) {

    notification.success({ message: message });
}
