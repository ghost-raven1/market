export interface User {
  id: number;
  name: string;
  email: string;
  created_at: string;
  rating: number;
  advertisements_count: number;
}

export interface Category {
  id: number;
  name: string;
}

export interface AdvertisementImage {
  path: string;
}

export interface Advertisement {
  id: number;
  title: string;
  description: string;
  price: number;
  category: Category;
  images: AdvertisementImage[];
  address: string;
  latitude: number;
  longitude: number;
  is_vip: boolean;
  is_favorite: boolean;
  rating: number;
  ratings_count: number;
  user: User;
  created_at: string;
  updated_at: string;
} 