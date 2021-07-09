import moment from 'moment'

export const formatTimestamp = (timestamp, format = 'HH:mm:ss YYYY-MM-DD') => moment(timestamp).format(format)
