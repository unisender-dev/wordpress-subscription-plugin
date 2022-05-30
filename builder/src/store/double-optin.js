export default {
    state: {
        doubleOptin: [
            {
                value: 3,
                label: 'У меня уже есть согласие контакта. Добавлять контакт со статусом «новый».'
            },
            {
                value: 0,
                label: 'Контакт только высказал желание подписаться, но ещё не подтвердил подписку. В этом случае контакту будет отправлено письмо-приглашение от Unisender подписаться.'
            },
            {
                value: 4,
                label: 'Система выполняет проверку на наличие контакта в ваших списках. Если контакт уже есть в ваших списках со статусом «новый» или «активен», то адрес просто будет добавлен в указанный вами список. Если же контакт отсутствует в ваших списках или его статус отличен от «новый» или «активен», то ему будет отправлено письмо-приглашение подписаться.'
            }
        ]
    },
    mutations: {},
    actions: {},
    getters: {
        doubleOptin(state) {
            return state.doubleOptin
        }
    }
}