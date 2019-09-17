module.exports = {
  devServer: {
    proxy: {
      // 配置跨域
      '/testapi': {
        target: 'https://ele-interface.herokuapp.com/api/',
        changOrigin: true,
        pathRewrite: {
          '^/testapi': ''
        }
      },
      '/api': {
        target: 'http://takeawayapi.pykky.com/',
        changOrigin: true,
        pathRewrite: {
          '^/api': ''
        }
      }
    },
    before: app => {}
  }
};
