import { defineStore } from "pinia";

export type NoticeType = 'success' | 'error' | 'info';

export type Notice = {
  id: number;
  type: NoticeType;
  message: string;
  timeout?: number;
};

let _id = 1;

export const useNotifyStore = defineStore('notify', {
  state: () => ({
    items: [] as Notice[],
  }),

  actions: {
    push(type: NoticeType, message: string, timeout = 3000) {
      const id = _id++;
      const notice: Notice = {id, type, message, timeout};

      this.items.push(notice);

      if (timeout > 0) {
        setTimeout(() => this.remove(id), timeout);
      }

      return id;
    },

    remove(id: number) {
      this.items = this.items.filter(n => n.id !== id);
    },

    success(msg: string, timeout?: number) {
      return this.push('success', msg, timeout)
    },

    error(msg: string, timeout?: number) {
      return this.push('error', msg, timeout)
    },

    info(msg: string, timeout?: number) {
      return this.push('info', msg, timeout)
    },
  }
});
