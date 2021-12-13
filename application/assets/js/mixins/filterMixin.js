export default {
  methods: {
    formatQueryParams(filters) {
      const params = new URLSearchParams();

      for (const [filter, value] of Object.entries(filters)) {

        if (value instanceof Array && value.length !== 0) {
          for (const item of value) {
            params.append(filter, item);
          }
        } else if (!(value instanceof Array) && value) {
          params.append(filter, value);
        }
      }

      return params.toString();
    },
  }
}
