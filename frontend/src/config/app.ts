export const config = {
  domain: process.env.VITE_APP_DOMAIN || 'your-domain.com',
  apiUrl: process.env.VITE_API_URL || 'https://your-domain.com/api',
  title: 'Market - Онлайн-рынок',
  description: 'Покупайте и продавайте товары на нашей платформе. Безопасные сделки, широкий выбор категорий и удобный интерфейс.',
  keywords: 'онлайн-рынок, купить, продать, товары, безопасные сделки',
  author: 'Market',
  socialImage: '/og-image.jpg',
  sitemap: {
    defaultPriority: 0.5,
    defaultChangeFreq: 'weekly',
    paths: [
      {
        path: '/',
        priority: 1.0,
        changeFreq: 'daily'
      },
      {
        path: '/advertisements',
        priority: 0.9,
        changeFreq: 'hourly'
      },
      {
        path: '/categories',
        priority: 0.8,
        changeFreq: 'weekly'
      },
      {
        path: '/profile',
        priority: 0.7,
        changeFreq: 'weekly'
      },
      {
        path: '/messages',
        priority: 0.6,
        changeFreq: 'daily'
      },
      {
        path: '/notifications',
        priority: 0.6,
        changeFreq: 'daily'
      }
    ]
  }
} as const; 