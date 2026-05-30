import axios from 'axios'

const axiosIns = axios.create({
// You can add your headers here
// ================================
// baseURL: 'https://some-domain.com/api/',
// timeout: 1000,
// headers: {'X-Custom-Header': 'foobar'}
})

axiosIns.interceptors.request.use(config => {
  const storedToken = localStorage.getItem('accessToken')
  const token = storedToken ? storedToken.replace(/^"+|"+$/g, '') : null

  if (token && !config.headers?.Authorization) {
    config.headers.Authorization = token.startsWith('Bearer ')
      ? token
      : `Bearer ${token}`
  }

  return config
})

export default axiosIns
