module.exports = {
  devServer: {
/*     open: true,
    host: 'localhost',
    port: 8080,
    https: false,
    hotOnly: false, */
    proxy: {
      // 配置跨域
      '/testapi': {
        target: 'https://ele-interface.herokuapp.com/api/',
        ws: true,
        changOrigin: true,
        pathRewrite: {
          '^/testapi': ''
        }
      },
      '/api': {
        target: 'http://takeawayapi.pykky.com/',
        ws: true,
        changOrigin: true,
        pathRewrite: {
          '^/api': ''
        }
      }
    }/*,
    before: app => {} */
  }
};
