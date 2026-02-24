import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export interface Product {
    id: number
    name: string
    price: number
    oldPrice?: number
    image: string
    rating: number
    reviewCount: number
    isHot?: boolean
    isNew?: boolean
    isSale?: boolean
    discount?: number
    category?: string
    brand?: string
}

export const useProductStore = defineStore('products', () => {
    const products = ref<Product[]>([
        {
            id: 1,
            name: 'Ốp lưng iPhone 15 Pro Max MagSafe',
            price: 299000,
            image: 'https://images.unsplash.com/photo-1605236453806-6ff36851218e?w=400&h=400&fit=crop',
            rating: 5,
            reviewCount: 128,
            isHot: true,
            category: 'Ốp lưng',
            brand: 'Apple'
        },
        {
            id: 2,
            name: 'Củ sạc nhanh 65W GaN USB-C',
            price: 450000,
            image: 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=400&h=400&fit=crop',
            rating: 5,
            reviewCount: 95,
            isNew: true,
            category: 'Sạc nhanh',
            brand: 'Anker'
        },
        {
            id: 3,
            name: 'Tai nghe Bluetooth Pro ANC',
            price: 899000,
            oldPrice: 1290000,
            image: 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&h=400&fit=crop',
            rating: 4,
            reviewCount: 76,
            isSale: true,
            discount: 30,
            category: 'Tai nghe',
            brand: 'Samsung'
        },
        {
            id: 4,
            name: 'Pin dự phòng 20000mAh PD 65W',
            price: 890000,
            image: 'https://images.unsplash.com/photo-1585338107529-13afc5f02586?w=400&h=400&fit=crop',
            rating: 5,
            reviewCount: 142,
            category: 'Pin dự phòng',
            brand: 'Baseus'
        },
        {
            id: 5,
            name: 'Cáp sạc Type-C to Lightning 2m',
            price: 189000,
            image: 'https://images.unsplash.com/photo-1600541519467-937869997e34?w=400&h=400&fit=crop',
            rating: 5,
            reviewCount: 234,
            isHot: true,
            category: 'Cáp sạc',
            brand: 'Xiaomi'
        },
        {
            id: 6,
            name: 'Kính cường lực 9D Full màn',
            price: 99000,
            image: 'https://images.unsplash.com/photo-1560343090-f0409e92791a?w=400&h=400&fit=crop',
            rating: 4,
            reviewCount: 312,
            category: 'Phụ kiện khác',
            brand: 'Baseus'
        },
        {
            id: 7,
            name: 'Dây đeo Apple Watch Sport',
            price: 249000,
            image: 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=400&h=400&fit=crop',
            rating: 5,
            reviewCount: 89,
            isNew: true,
            category: 'Phụ kiện khác',
            brand: 'Apple'
        },
        {
            id: 8,
            name: 'Giá đỡ điện thoại MagSafe ô tô',
            price: 239000,
            oldPrice: 399000,
            image: 'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?w=400&h=400&fit=crop',
            rating: 5,
            reviewCount: 67,
            isSale: true,
            discount: 40,
            category: 'Phụ kiện khác',
            brand: 'Baseus'
        },
        {
            id: 9,
            name: 'Sạc không dây MagSafe 15W',
            price: 599000,
            image: 'https://images.unsplash.com/photo-1628815113969-0487917f26eb?w=400&h=400&fit=crop',
            rating: 4,
            reviewCount: 54,
            category: 'Sạc nhanh',
            brand: 'Apple'
        },
        {
            id: 10,
            name: 'Ốp lưng iPhone 16 Ultra Slim',
            price: 349000,
            image: 'https://images.unsplash.com/photo-1606229365485-93a3b8ee0385?w=400&h=400&fit=crop',
            rating: 5,
            reviewCount: 45,
            isHot: true,
            category: 'Ốp lưng',
            brand: 'Apple'
        },
        {
            id: 11,
            name: 'Loa Bluetooth JBL Flip 6',
            price: 2490000,
            image: 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=400&h=400&fit=crop',
            rating: 5,
            reviewCount: 78,
            category: 'Tai nghe',
            brand: 'JBL'
        },
        {
            id: 12,
            name: 'Hub USB-C 7 in 1 4K HDMI',
            price: 590000,
            oldPrice: 790000,
            image: 'https://images.unsplash.com/photo-1625772452859-1c03d5bf1137?w=400&h=400&fit=crop',
            rating: 5,
            reviewCount: 32,
            isSale: true,
            discount: 25,
            category: 'Phụ kiện khác',
            brand: 'Baseus'
        }
    ])

    const loading = ref(false)

    const fetchProducts = async () => {
        loading.value = true
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 500))
        loading.value = false
    }

    return {
        products,
        loading,
        fetchProducts
    }
})
