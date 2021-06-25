import moment from 'moment'

export const formatTimestamp = (timestamp) => moment(timestamp).format('HH:mm:ss YYYY-MM-DD')
