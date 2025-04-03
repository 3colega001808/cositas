const vehiclesData = [
  {
    id: 1,
    name: "Porsche 911 Carrera S",
    type: "Deportivo",
    year: 2023,
    brand: "porsche",
    plan: "elite",
    image:
      "https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1770&auto=format&fit=crop",
    power: "450 CV",
    acceleration: "3.5s",
    traction: "Trasera",
    details: {
      description:
        "El Porsche 911 es uno de los deportivos más emblemáticos de la historia, reconocido por su impresionante rendimiento, manejo excepcional y diseño atemporal.",
      specs: [
        { name: "Potencia", value: "450 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "3.5s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "308 km/h", icon: "bi-trophy" },
        { name: "Tracción", value: "Trasera", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "PDK 8 vel.",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "9.4 l/100km", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1580274455191-1c62238fa333?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1511919884226-fd3cad34687c?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1983&auto=format&fit=crop"
      ]
    },
    reviews: [
      {
        user: "Miguel Ángel",
        avatar: "https://i.pravatar.cc/150?img=11",
        rating: 5,
        comment:
          "El 911 es una máquina; cada curva te hace vibrar. Un clásico que nunca decepciona."
      },
      {
        user: "Laura",
        avatar: "https://i.pravatar.cc/150?img=5",
        rating: 4,
        comment:
          "Potente y preciso, aunque el precio es de locos. Sin duda, un coche para los que pueden permitírselo."
      }
    ]
  },
  {
    id: 2,
    name: "Audi RS6 Avant",
    type: "Familiar Deportivo",
    year: 2023,
    brand: "audi",
    plan: "premium",
    image:
      "https://images.unsplash.com/photo-1580274455191-1c62238fa333?q=80&w=1770&auto=format&fit=crop",
    power: "600 CV",
    acceleration: "3.6s",
    traction: "Quattro",
    details: {
      description:
        "El Audi RS6 Avant redefine el concepto de familiar deportivo con su potencia impresionante y tracción Quattro, combinando practicidad con deportividad extrema.",
      specs: [
        { name: "Potencia", value: "600 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "3.6s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "305 km/h", icon: "bi-trophy" },
        { name: "Tracción", value: "Quattro", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Tiptronic 8 vel.",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "11.7 l/100km", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://images.unsplash.com/photo-1580274455191-1c62238fa333?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1511919884226-fd3cad34687c?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1983&auto=format&fit=crop"
      ]
    },
    reviews: [
      {
        user: "Carlos",
        avatar: "https://i.pravatar.cc/150?img=12",
        rating: 5,
        comment:
          "Una bestia familiar que se comporta como un deportivo en pista. Increíble versatilidad."
      },
      {
        user: "Elena",
        avatar: "https://i.pravatar.cc/150?img=9",
        rating: 4,
        comment:
          "Confort y potencia en perfecta armonía, aunque su consumo es un punto a tener en cuenta."
      }
    ]
  },
  {
    id: 3,
    name: "BMW M4 Competition",
    type: "Coupé Deportivo",
    year: 2023,
    brand: "bmw",
    plan: "premium",
    image:
      "https://images.unsplash.com/photo-1555652736-e92021d28a10?q=80&w=1964&auto=format&fit=crop",
    power: "510 CV",
    acceleration: "3.9s",
    traction: "Trasera",
    details: {
      description:
        "El BMW M4 Competition es la máxima expresión del dinamismo deportivo, con un diseño agresivo y tecnología de vanguardia.",
      specs: [
        { name: "Potencia", value: "510 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "3.9s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "290 km/h", icon: "bi-trophy" },
        { name: "Tracción", value: "Trasera", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "M DCT 8 vel.",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "10.2 l/100km", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://images.unsplash.com/photo-1555652736-e92021d28a10?q=80&w=1964&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1511919884226-fd3cad34687c?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1983&auto=format&fit=crop"
      ]
    },
    reviews: [
      {
        user: "Javier",
        avatar: "https://i.pravatar.cc/150?img=8",
        rating: 4,
        comment:
          "Imponente en cada aceleración, aunque su diseño frontal genera opiniones divididas."
      },
      {
        user: "Ana",
        avatar: "https://i.pravatar.cc/150?img=3",
        rating: 5,
        comment:
          "Cada aceleración es pura adrenalina. Un deportivo que te hace vibrar desde el primer instante."
      }
    ]
  },
  {
    id: 4,
    name: "Mercedes-AMG GT-R",
    type: "Deportivo",
    year: 2023,
    brand: "mercedes",
    plan: "elite",
    image:
      "https://images.unsplash.com/photo-1618863099278-75222d755814?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    power: "530 CV",
    acceleration: "3.8s",
    traction: "Trasera",
    details: {
      description:
        "El Mercedes-AMG GT combina elegancia y deportividad en un coupé de proporciones perfectas y rendimiento excepcional.",
      specs: [
        { name: "Potencia", value: "530 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "3.8s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "312 km/h", icon: "bi-trophy" },
        { name: "Tracción", value: "Trasera", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "MCT 9G",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "11.4 l/100km", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://images.unsplash.com/photo-1617814076668-11183441b88b?q=80&w=1965&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1511919884226-fd3cad34687c?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1983&auto=format&fit=crop"
      ]
    },
    reviews: [
      {
        user: "Roberto",
        avatar: "https://i.pravatar.cc/150?img=15",
        rating: 5,
        comment:
          "Elegancia y fuerza en cada arranque. Un deportivo que se nota en la carretera."
      },
      {
        user: "María",
        avatar: "https://i.pravatar.cc/150?img=10",
        rating: 4,
        comment:
          "Un coche espectacular con un infoentretenimiento que podría pulirse un poco."
      }
    ]
  },
  {
    id: 7,
    name: "Audi Q5 Sportback",
    type: "SUV",
    year: 2023,
    brand: "audi",
    plan: "basico",
    image:
      "https://images.unsplash.com/photo-1541348263662-e068662d82af?q=80&w=1772&auto=format&fit=crop",
    power: "204 CV",
    acceleration: "7.3s",
    traction: "Quattro",
    details: {
      description:
        "El Audi Q5 Sportback combina la versatilidad de un SUV con el dinamismo de un coupé, ofreciendo potencia y un diseño elegante.",
      specs: [
        { name: "Potencia", value: "204 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "7.3s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "222 km/h", icon: "bi-trophy" },
        { name: "Tracción", value: "Quattro", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "S tronic 7 vel.",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "7.6 l/100km", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://images.unsplash.com/photo-1541348263662-e068662d82af?q=80&w=1772&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1511919884226-fd3cad34687c?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1983&auto=format&fit=crop"
      ]
    },
    reviews: [
      {
        user: "Raúl",
        avatar: "https://i.pravatar.cc/150?img=16",
        rating: 4,
        comment:
          "Un SUV dinámico que sorprende en cada viaje, aunque a veces se siente un poco rígido."
      },
      {
        user: "Isabel",
        avatar: "https://i.pravatar.cc/150?img=4",
        rating: 5,
        comment:
          "Versátil y con un diseño que destaca, ideal para cualquier terreno."
      }
    ]
  },
  {
    id: 8,
    name: "Mercedes-Benz Clase C",
    type: "Sedán",
    year: 2023,
    brand: "mercedes",
    plan: "basico",
    image:
      "https://images.unsplash.com/photo-1617813960581-3189103b8a74?q=80&w=1974&auto=format&fit=crop",
    power: "204 CV",
    acceleration: "7.3s",
    traction: "Trasera",
    details: {
      description:
        "El Mercedes-Benz Clase C es un sedán compacto de lujo que combina tecnología de la Clase S con un formato más accesible y refinado.",
      specs: [
        { name: "Potencia", value: "204 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "7.3s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "246 km/h", icon: "bi-trophy" },
        { name: "Tracción", value: "Trasera", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "9G-TRONIC",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "6.8 l/100km", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://images.unsplash.com/photo-1617813960581-3189103b8a74?q=80&w=1974&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1511919884226-fd3cad34687c?q=80&w=1770&auto=format&fit=crop",
        "https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?q=80&w=1983&auto=format&fit=crop"
      ]
    },
    reviews: [
      {
        user: "Sergio",
        avatar: "https://i.pravatar.cc/150?img=18",
        rating: 5,
        comment:
          "El Clase C es sofisticado y potente, una delicia al volante."
      },
      {
        user: "Carmen",
        avatar: "https://i.pravatar.cc/150?img=2",
        rating: 4,
        comment:
          "Un sedán que combina lujo y rendimiento, aunque algunos detalles podrían mejorar."
      }
    ]
  },
  // Vehículos adaptados del JS 2 (IDs 9 en adelante)
  {
    id: 9,
    name: "Mazda MX‑5 Miata",
    type: "Convertible Deportivo",
    year: 2022,
    brand: "mazda",
    plan: "basico",
    image:
      "https://media.ed.edmunds-media.com/mazda/hero/mazda_hero_502_1600.jpg",
    power: "181 CV",
    acceleration: "6.5s",
    traction: "RWD",
    details: {
      description:
        "El Mazda MX‑5 Miata, convertible deportivo del 2022, destaca por su agilidad y la sensación inigualable de conducirlo.",
      specs: [
        { name: "Potencia", value: "181 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "6.5s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "RWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Manual",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://media.ed.edmunds-media.com/mazda/hero/mazda_hero_502_1600.jpg",
        "https://media.ed.edmunds-media.com/mazda/hero/mazda_hero_502_1600.jpg",
        "https://media.ed.edmunds-media.com/mazda/hero/mazda_hero_502_1600.jpg",
        "https://media.ed.edmunds-media.com/mazda/hero/mazda_hero_502_1600.jpg"
      ]
    },
    reviews: [
      {
        user: "Carlos",
        avatar: "https://i.pravatar.cc/150?img=21",
        rating: 5,
        comment:
          "Conducir el MX‑5 es una experiencia única; se siente vivo en cada curva."
      },
      {
        user: "Lucía",
        avatar: "https://i.pravatar.cc/150?img=22",
        rating: 4,
        comment:
          "Ágil y divertido, perfecto para escapadas de fin de semana aunque el espacio es reducido."
      }
    ]
  },
  {
    id: 10,
    name: "Toyota GR86",
    type: "Coupé Deportivo",
    year: 2022,
    brand: "toyota",
    plan: "basico",
    image:
      "https://ddztmb1ahc6o7.cloudfront.net/gatewaytoyota/wp-content/uploads/2022/03/07145249/2022_GR86_Base_TruenoBlue_021_mid.jpg",
    power: "228 CV",
    acceleration: "6.1s",
    traction: "RWD",
    details: {
      description:
        "El Toyota GR86 es un coupé deportivo del 2022, ágil y con un sonido que emociona.",
      specs: [
        { name: "Potencia", value: "228 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "6.1s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "RWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Manual",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://ddztmb1ahc6o7.cloudfront.net/gatewaytoyota/wp-content/uploads/2022/03/07145249/2022_GR86_Base_TruenoBlue_021_mid.jpg",
        "https://ddztmb1ahc6o7.cloudfront.net/gatewaytoyota/wp-content/uploads/2022/03/07145249/2022_GR86_Base_TruenoBlue_021_mid.jpg",
        "https://ddztmb1ahc6o7.cloudfront.net/gatewaytoyota/wp-content/uploads/2022/03/07145249/2022_GR86_Base_TruenoBlue_021_mid.jpg",
        "https://ddztmb1ahc6o7.cloudfront.net/gatewaytoyota/wp-content/uploads/2022/03/07145249/2022_GR86_Base_TruenoBlue_021_mid.jpg"
      ]
    },
    reviews: [
      {
        user: "Miguel",
        avatar: "https://i.pravatar.cc/150?img=23",
        rating: 5,
        comment:
          "El GR86 es un verdadero coupé, ágil y con un sonido que te emociona."
      },
      {
        user: "Sofía",
        avatar: "https://i.pravatar.cc/150?img=24",
        rating: 4,
        comment:
          "Excelente manejo y diseño; ideal para quienes disfrutan de la conducción deportiva."
      }
    ]
  },
  {
    id: 11,
    name: "Subaru BRZ",
    type: "Coupé Deportivo",
    year: 2022,
    brand: "subaru",
    plan: "basico",
    image:
      "https://ecoloauto.com/wp-content/uploads/2022-Subaru-BRZ-Sport-tech-01-MD.jpg",
    power: "228 CV",
    acceleration: "6.2s",
    traction: "RWD",
    details: {
      description:
        "El Subaru BRZ es un coupé deportivo que destaca por su equilibrio y manejo dinámico.",
      specs: [
        { name: "Potencia", value: "228 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "6.2s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "RWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Manual",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://ecoloauto.com/wp-content/uploads/2022-Subaru-BRZ-Sport-tech-01-MD.jpg",
        "https://ecoloauto.com/wp-content/uploads/2022-Subaru-BRZ-Sport-tech-01-MD.jpg",
        "https://ecoloauto.com/wp-content/uploads/2022-Subaru-BRZ-Sport-tech-01-MD.jpg",
        "https://ecoloauto.com/wp-content/uploads/2022-Subaru-BRZ-Sport-tech-01-MD.jpg"
      ]
    },
    reviews: [
      {
        user: "Javi",
        avatar: "https://i.pravatar.cc/150?img=25",
        rating: 4,
        comment:
          "El BRZ es muy equilibrado, perfecto para curvas, aunque le falta fuerza en rectas."
      },
      {
        user: "Elena",
        avatar: "https://i.pravatar.cc/150?img=26",
        rating: 5,
        comment:
          "Divertido y ágil; te conecta de inmediato con la carretera."
      }
    ]
  },
  {
    id: 12,
    name: "Fiat 124 Spider",
    type: "Convertible Deportivo",
    year: 2019,
    brand: "fiat",
    plan: "basico",
    image:
      "https://carsguide-res.cloudinary.com/image/upload/f_auto,fl_lossy,q_auto,t_default/v1/editorial/abarth-124-spider-monza-my19-1200x800-(19).jpg",
    power: "164 CV",
    acceleration: "6.8s",
    traction: "RWD",
    details: {
      description:
        "El Fiat 124 Spider es un convertible deportivo con estilo clásico y una conducción divertida.",
      specs: [
        { name: "Potencia", value: "164 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "6.8s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "RWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Manual",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://carsguide-res.cloudinary.com/image/upload/f_auto,fl_lossy,q_auto,t_default/v1/editorial/abarth-124-spider-monza-my19-1200x800-(19).jpg",
        "https://carsguide-res.cloudinary.com/image/upload/f_auto,fl_lossy,q_auto,t_default/v1/editorial/abarth-124-spider-monza-my19-1200x800-(19).jpg",
        "https://carsguide-res.cloudinary.com/image/upload/f_auto,fl_lossy,q_auto,t_default/v1/editorial/abarth-124-spider-monza-my19-1200x800-(19).jpg",
        "https://carsguide-res.cloudinary.com/image/upload/f_auto,fl_lossy,q_auto,t_default/v1/editorial/abarth-124-spider-monza-my19-1200x800-(19).jpg"
      ]
    },
    reviews: [
      {
        user: "Andrés",
        avatar: "https://i.pravatar.cc/150?img=27",
        rating: 4,
        comment:
          "Un convertible con estilo clásico y mucha diversión al volante."
      },
      {
        user: "Marta",
        avatar: "https://i.pravatar.cc/150?img=28",
        rating: 3,
        comment:
          "Bonito y ágil, pero el rendimiento es algo moderado para su categoría."
      }
    ]
  },
  {
    id: 13,
    name: "Nissan 370Z",
    type: "Coupé Deportivo",
    year: 2020,
    brand: "nissan",
    plan: "basico",
    image:
      "https://www.topgear.es/sites/navi.axelspringer.es/public/media/image/2023/07/nissan-370z-3080192.jpg",
    power: "332 CV",
    acceleration: "5.2s",
    traction: "RWD",
    details: {
      description:
        "El Nissan 370Z es un coupé deportivo reconocido por su potente motor y diseño agresivo.",
      specs: [
        { name: "Potencia", value: "332 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "5.2s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "RWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Manual",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://www.topgear.es/sites/navi.axelspringer.es/public/media/image/2023/07/nissan-370z-3080192.jpg",
        "https://www.topgear.es/sites/navi.axelspringer.es/public/media/image/2023/07/nissan-370z-3080192.jpg",
        "https://www.topgear.es/sites/navi.axelspringer.es/public/media/image/2023/07/nissan-370z-3080192.jpg",
        "https://www.topgear.es/sites/navi.axelspringer.es/public/media/image/2023/07/nissan-370z-3080192.jpg"
      ]
    },
    reviews: [
      {
        user: "Roberto",
        avatar: "https://i.pravatar.cc/150?img=29",
        rating: 5,
        comment:
          "El 370Z tiene un carácter fuerte y un sonido espectacular que te atrapa desde el primer rugido."
      },
      {
        user: "Valeria",
        avatar: "https://i.pravatar.cc/150?img=30",
        rating: 4,
        comment:
          "Potente y agresivo en diseño, aunque el confort en largas distancias podría mejorar."
      }
    ]
  },
  {
    id: 14,
    name: "BMW Z4",
    type: "Convertible Deportivo",
    year: 2021,
    brand: "bmw",
    plan: "basico",
    image:
      "https://mediapool.bmwgroup.com/cache/P9/202209/P90479446/P90479446-bmw-z4-m40i-09-2022-600px.jpg",
    power: "255 CV",
    acceleration: "5.4s",
    traction: "RWD",
    details: {
      description:
        "El BMW Z4 es un convertible deportivo que combina lujo y dinamismo con un diseño elegante.",
      specs: [
        { name: "Potencia", value: "255 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "5.4s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "RWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Automática",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://mediapool.bmwgroup.com/cache/P9/202209/P90479446/P90479446-bmw-z4-m40i-09-2022-600px.jpg",
        "https://mediapool.bmwgroup.com/cache/P9/202209/P90479446/P90479446-bmw-z4-m40i-09-2022-600px.jpg",
        "https://mediapool.bmwgroup.com/cache/P9/202209/P90479446/P90479446-bmw-z4-m40i-09-2022-600px.jpg",
        "https://mediapool.bmwgroup.com/cache/P9/202209/P90479446/P90479446-bmw-z4-m40i-09-2022-600px.jpg"
      ]
    },
    reviews: [
      {
        user: "Diego",
        avatar: "https://i.pravatar.cc/150?img=31",
        rating: 5,
        comment:
          "El Z4 es elegante y ágil; una mezcla perfecta de lujo y deportividad."
      },
      {
        user: "Carla",
        avatar: "https://i.pravatar.cc/150?img=32",
        rating: 4,
        comment:
          "Un convertible premium que ofrece una conducción emocionante, aunque su precio es algo elevado."
      }
    ]
  },
  {
    id: 15,
    name: "Honda Civic Type R",
    type: "Hatchback Deportivo",
    year: 2022,
    brand: "honda",
    plan: "basico",
    image:
      "https://wieck-honda-production.s3.amazonaws.com/photos/aaf14fc8ae21ef4a43b40394791a7089a9be5638/preview-928x522.jpg",
    power: "320 CV",
    acceleration: "5.8s",
    traction: "FWD",
    details: {
      description:
        "El Honda Civic Type R es un hatchback deportivo conocido por su agresividad y su increíble respuesta en pista.",
      specs: [
        { name: "Potencia", value: "320 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "5.8s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "FWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Manual",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://wieck-honda-production.s3.amazonaws.com/photos/aaf14fc8ae21ef4a43b40394791a7089a9be5638/preview-928x522.jpg",
        "https://wieck-honda-production.s3.amazonaws.com/photos/aaf14fc8ae21ef4a43b40394791a7089a9be5638/preview-928x522.jpg",
        "https://wieck-honda-production.s3.amazonaws.com/photos/aaf14fc8ae21ef4a43b40394791a7089a9be5638/preview-928x522.jpg",
        "https://wieck-honda-production.s3.amazonaws.com/photos/aaf14fc8ae21ef4a43b40394791a7089a9be5638/preview-928x522.jpg"
      ]
    },
    reviews: [
      {
        user: "Pablo",
        avatar: "https://i.pravatar.cc/150?img=33",
        rating: 5,
        comment:
          "El Type R es puro rendimiento; cada aceleración te inyecta adrenalina."
      },
      {
        user: "Noelia",
        avatar: "https://i.pravatar.cc/150?img=34",
        rating: 4,
        comment:
          "Un hatchback sorprendente en pista, con un estilo agresivo y moderno."
      }
    ]
  },
  {
    id: 16,
    name: "Volkswagen Golf GTI",
    type: "Hatchback Deportivo",
    year: 2022,
    brand: "volkswagen",
    plan: "basico",
    image:
      "https://www.cnet.com/a/img/resize/d0e13c91f648afcf4718f486d294c450f5ec3fd0/hub/2022/03/30/4d055b2e-d0c6-4858-9208-5818efeeadc3/2022-vw-golf-gti-long-term-009.jpg?auto=webp&width=1200",
    power: "241 CV",
    acceleration: "6.3s",
    traction: "FWD",
    details: {
      description:
        "El Golf GTI es un hatchback deportivo moderno, reconocido por su agilidad y carácter en cada conducción.",
      specs: [
        { name: "Potencia", value: "241 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "6.3s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "FWD", icon: "bi-gear" },
        { name: "Transmisión", value: "DSG", icon: "bi-arrows-fullscreen" },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://www.cnet.com/a/img/resize/d0e13c91f648afcf4718f486d294c450f5ec3fd0/hub/2022/03/30/4d055b2e-d0c6-4858-9208-5818efeeadc3/2022-vw-golf-gti-long-term-009.jpg?auto=webp&width=1200",
        "https://www.cnet.com/a/img/resize/d0e13c91f648afcf4718f486d294c450f5ec3fd0/hub/2022/03/30/4d055b2e-d0c6-4858-9208-5818efeeadc3/2022-vw-golf-gti-long-term-009.jpg?auto=webp&width=1200",
        "https://www.cnet.com/a/img/resize/d0e13c91f648afcf4718f486d294c450f5ec3fd0/hub/2022/03/30/4d055b2e-d0c6-4858-9208-5818efeeadc3/2022-vw-golf-gti-long-term-009.jpg?auto=webp&width=1200",
        "https://www.cnet.com/a/img/resize/d0e13c91f648afcf4718f486d294c450f5ec3fd0/hub/2022/03/30/4d055b2e-d0c6-4858-9208-5818efeeadc3/2022-vw-golf-gti-long-term-009.jpg?auto=webp&width=1200"
      ]
    },
    reviews: [
      {
        user: "Eduardo",
        avatar: "https://i.pravatar.cc/150?img=35",
        rating: 4,
        comment:
          "Un GTI con mucha personalidad, ideal para los amantes de lo deportivo."
      },
      {
        user: "Andrea",
        avatar: "https://i.pravatar.cc/150?img=36",
        rating: 3,
        comment:
          "Buen manejo y estilo, aunque le falta un poco de refinamiento en el interior."
      }
    ]
  },
  {
    id: 17,
    name: "Ford Mustang EcoBoost",
    type: "Coupé Deportivo",
    year: 2021,
    brand: "ford",
    plan: "basico",
    image:
      "https://carsguide-res.cloudinary.com/image/upload/f_auto,fl_lossy,q_auto,t_default/v1/editorial/review/hero_image/2021-Ford-Mustang-GT-auto=green-coupe-peter-anderson-1001x565-(1).jpg",
    power: "310 CV",
    acceleration: "5.5s",
    traction: "RWD",
    details: {
      description:
        "El Ford Mustang EcoBoost es un ícono del deporte americano, combinando potencia y diseño icónico.",
      specs: [
        { name: "Potencia", value: "310 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "5.5s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "RWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Automática",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://carsguide-res.cloudinary.com/image/upload/f_auto,fl_lossy,q_auto,t_default/v1/editorial/review/hero_image/2021-Ford-Mustang-GT-auto=green-coupe-peter-anderson-1001x565-(1).jpg",
        "https://carsguide-res.cloudinary.com/image/upload/f_auto,fl_lossy,q_auto,t_default/v1/editorial/review/hero_image/2021-Ford-Mustang-GT-auto=green-coupe-peter-anderson-1001x565-(1).jpg",
        "https://carsguide-res.cloudinary.com/image/upload/f_auto,fl_lossy,q_auto,t_default/v1/editorial/review/hero_image/2021-Ford-Mustang-GT-auto=green-coupe-peter-anderson-1001x565-(1).jpg",
        "https://carsguide-res.cloudinary.com/image/upload/f_auto,fl_lossy,q_auto,t_default/v1/editorial/review/hero_image/2021-Ford-Mustang-GT-auto=green-coupe-peter-anderson-1001x565-(1).jpg"
      ]
    },
    reviews: [
      {
        user: "Ignacio",
        avatar: "https://i.pravatar.cc/150?img=37",
        rating: 5,
        comment:
          "El Mustang EcoBoost es un ícono; su rugido te hace sentir la potencia en cada aceleración."
      },
      {
        user: "Lorena",
        avatar: "https://i.pravatar.cc/150?img=38",
        rating: 4,
        comment:
          "Un clásico renovado que combina estilo y potencia, aunque a veces es rebelde en ciudad."
      }
    ]
  },
  {
    id: 18,
    name: "Hyundai Veloster N",
    type: "Hatchback Deportivo",
    year: 2022,
    brand: "hyundai",
    plan: "basico",
    image:
      "https://www.cnet.com/a/img/resize/287dfaf309b3fed59f9933c8e5899620c56ea09d/hub/2022/04/19/8da47739-c866-4aff-9be9-1ba2d1b8f9d9/ogi-2022-hyundai-veloster-n-42.jpg?auto=webp&fit=crop&height=675&width=1200",
    power: "275 CV",
    acceleration: "5.6s",
    traction: "FWD",
    details: {
      description:
        "El Hyundai Veloster N es un hatchback atrevido, dinámico y diseñado para ofrecer una conducción muy divertida.",
      specs: [
        { name: "Potencia", value: "275 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "5.6s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "FWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Manual",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://www.cnet.com/a/img/resize/287dfaf309b3fed59f9933c8e5899620c56ea09d/hub/2022/04/19/8da47739-c866-4aff-9be9-1ba2d1b8f9d9/ogi-2022-hyundai-veloster-n-42.jpg?auto=webp&fit=crop&height=675&width=1200",
        "https://www.cnet.com/a/img/resize/287dfaf309b3fed59f9933c8e5899620c56ea09d/hub/2022/04/19/8da47739-c866-4aff-9be9-1ba2d1b8f9d9/ogi-2022-hyundai-veloster-n-42.jpg?auto=webp&fit=crop&height=675&width=1200",
        "https://www.cnet.com/a/img/resize/287dfaf309b3fed59f9933c8e5899620c56ea09d/hub/2022/04/19/8da47739-c866-4aff-9be9-1ba2d1b8f9d9/ogi-2022-hyundai-veloster-n-42.jpg?auto=webp&fit=crop&height=675&width=1200",
        "https://www.cnet.com/a/img/resize/287dfaf309b3fed59f9933c8e5899620c56ea09d/hub/2022/04/19/8da47739-c866-4aff-9be9-1ba2d1b8f9d9/ogi-2022-hyundai-veloster-n-42.jpg?auto=webp&fit=crop&height=675&width=1200"
      ]
    },
    reviews: [
      {
        user: "Víctor",
        avatar: "https://i.pravatar.cc/150?img=39",
        rating: 5,
        comment:
          "El Veloster N es muy divertido de conducir; se nota en cada cambio y curva."
      },
      {
        user: "Sonia",
        avatar: "https://i.pravatar.cc/150?img=40",
        rating: 4,
        comment:
          "Dinámico y atrevido, aunque en viajes largos la comodidad podría ser mejor."
      }
    ]
  },
  // Grupo: premium
  {
    id: 19,
    name: "Lancia Delta HF Integrale",
    type: "Clásico Rally",
    year: 1992,
    brand: "lancia",
    plan: "premium",
    image:
      "https://uncrate.com/p/2017/11/lancia-delt-hf-integrale-evo-1.jpg",
    power: "212 CV",
    acceleration: "5.7s",
    traction: "AWD",
    details: {
      description:
        "La Lancia Delta HF Integrale es un clásico rally del 1992, reconocida por su manejo y herencia en competición.",
      specs: [
        { name: "Potencia", value: "212 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "5.7s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "AWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Manual",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://uncrate.com/p/2017/11/lancia-delt-hf-integrale-evo-1.jpg",
        "https://uncrate.com/p/2017/11/lancia-delt-hf-integrale-evo-1.jpg",
        "https://uncrate.com/p/2017/11/lancia-delt-hf-integrale-evo-1.jpg",
        "https://uncrate.com/p/2017/11/lancia-delt-hf-integrale-evo-1.jpg"
      ]
    },
    reviews: [
      {
        user: "Marco",
        avatar: "https://i.pravatar.cc/150?img=41",
        rating: 4,
        comment:
          "Un rally legendario con un manejo muy conectado a la pista, aunque en carretera se nota su antigüedad."
      },
      {
        user: "Luciana",
        avatar: "https://i.pravatar.cc/150?img=42",
        rating: 3,
        comment:
          "Clásico en competición, pero en el día a día se queda corto."
      }
    ]
  },
  {
    id: 20,
    name: "BMW M240i",
    type: "Coupé Performance",
    year: 2022,
    brand: "bmw",
    plan: "premium",
    image:
      "https://images.turo.com/media/vehicle/images/DKr8lDXBTqivYrTTQhPbwA.jpg",
    power: "382 CV",
    acceleration: "4.3s",
    traction: "AWD",
    details: {
      description:
        "El BMW M240i es un coupé performance que combina un diseño agresivo con un rendimiento muy potente.",
      specs: [
        { name: "Potencia", value: "382 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "4.3s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "AWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Automática",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://images.turo.com/media/vehicle/images/DKr8lDXBTqivYrTTQhPbwA.jpg",
        "https://images.turo.com/media/vehicle/images/DKr8lDXBTqivYrTTQhPbwA.jpg",
        "https://images.turo.com/media/vehicle/images/DKr8lDXBTqivYrTTQhPbwA.jpg",
        "https://images.turo.com/media/vehicle/images/DKr8lDXBTqivYrTTQhPbwA.jpg"
      ]
    },
    reviews: [
      {
        user: "Alberto",
        avatar: "https://i.pravatar.cc/150?img=43",
        rating: 5,
        comment:
          "El M240i es rápido y tiene un diseño impecable, perfecto para los que buscan dinamismo."
      },
      {
        user: "Verónica",
        avatar: "https://i.pravatar.cc/150?img=44",
        rating: 4,
        comment:
          "Potente y agresivo en apariencia, aunque el precio puede ser un poco elevado."
      }
    ]
  },
  {
    id: 21,
    name: "Audi S3",
    type: "Compact Performance",
    year: 2022,
    brand: "audi",
    plan: "premium",
    image:
      "https://images.carexpert.com.au/resize/3000/-/app/uploads/2022/08/2022-Audi-S3-Sportback-HERO-2.jpg",
    power: "306 CV",
    acceleration: "4.8s",
    traction: "Quattro",
    details: {
      description:
        "El Audi S3 es un compacto performance que ofrece agilidad y un sonido del motor muy estimulante.",
      specs: [
        { name: "Potencia", value: "306 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "4.8s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "Quattro", icon: "bi-gear" },
        { name: "Transmisión", value: "S-Tronic", icon: "bi-arrows-fullscreen" },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://images.carexpert.com.au/resize/3000/-/app/uploads/2022/08/2022-Audi-S3-Sportback-HERO-2.jpg",
        "https://images.carexpert.com.au/resize/3000/-/app/uploads/2022/08/2022-Audi-S3-Sportback-HERO-2.jpg",
        "https://images.carexpert.com.au/resize/3000/-/app/uploads/2022/08/2022-Audi-S3-Sportback-HERO-2.jpg",
        "https://images.carexpert.com.au/resize/3000/-/app/uploads/2022/08/2022-Audi-S3-Sportback-HERO-2.jpg"
      ]
    },
    reviews: [
      {
        user: "Esteban",
        avatar: "https://i.pravatar.cc/150?img=45",
        rating: 4,
        comment:
          "El S3 es compacto y ágil, con un motor que te emociona, aunque el interior es un poco básico."
      },
      {
        user: "Patricia",
        avatar: "https://i.pravatar.cc/150?img=46",
        rating: 5,
        comment:
          "Conduce como un verdadero deportivo. Perfecto para la ciudad y para salidas a pista."
      }
    ]
  },
  // Grupo: elite
  {
    id: 22,
    name: "Porsche 992 GT3 Touring",
    type: "Superdeportivo",
    year: 2023,
    brand: "porsche",
    plan: "elite",
    image:
      "https://richmonds.com.au/wp-content/uploads/2024/02/Porsche-992-GT3-Silver-3.jpg",
    power: "510 CV",
    acceleration: "3.4s",
    traction: "RWD",
    details: {
      description:
        "El Porsche 992 GT3 Touring es un superdeportivo diseñado para ofrecer un rendimiento de alta gama y un estilo incomparable.",
      specs: [
        { name: "Potencia", value: "510 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "3.4s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "RWD", icon: "bi-gear" },
        { name: "Transmisión", value: "PDK", icon: "bi-arrows-fullscreen" },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://richmonds.com.au/wp-content/uploads/2024/02/Porsche-992-GT3-Silver-3.jpg",
        "https://richmonds.com.au/wp-content/uploads/2024/02/Porsche-992-GT3-Silver-3.jpg",
        "https://richmonds.com.au/wp-content/uploads/2024/02/Porsche-992-GT3-Silver-3.jpg",
        "https://richmonds.com.au/wp-content/uploads/2024/02/Porsche-992-GT3-Silver-3.jpg"
      ]
    },
    reviews: [
      {
        user: "Fernando",
        avatar: "https://i.pravatar.cc/150?img=47",
        rating: 5,
        comment:
          "Cada curva con el GT3 Touring es una explosión de adrenalina. Una máquina impecable."
      },
      {
        user: "Marina",
        avatar: "https://i.pravatar.cc/150?img=48",
        rating: 4,
        comment:
          "Impresionante en potencia y estilo, aunque el precio es solo para unos pocos."
      }
    ]
  },
  {
    id: 23,
    name: "Porsche 911 Carrera GTS",
    type: "Superdeportivo",
    year: 2022,
    brand: "porsche",
    plan: "elite",
    image:
      "https://images.unsplash.com/photo-1611651338412-8403fa6e3599?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80",
    power: "473 CV",
    acceleration: "3.3s",
    traction: "RWD",
    details: {
      description:
        "El Porsche 911 Carrera GTS es un superdeportivo del 2022 que combina un rendimiento brutal con un diseño icónico.",
      specs: [
        { name: "Potencia", value: "473 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "3.3s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "RWD", icon: "bi-gear" },
        { name: "Transmisión", value: "PDK", icon: "bi-arrows-fullscreen" },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://images.unsplash.com/photo-1611651338412-8403fa6e3599?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80",
        "https://images.unsplash.com/photo-1611651338412-8403fa6e3599?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80",
        "https://images.unsplash.com/photo-1611651338412-8403fa6e3599?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80",
        "https://images.unsplash.com/photo-1611651338412-8403fa6e3599?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80"
      ]
    },
    reviews: [
      {
        user: "Ricardo",
        avatar: "https://i.pravatar.cc/150?img=49",
        rating: 5,
        comment:
          "El 911 Carrera GTS es brutal, cada aceleración te deja sin aliento."
      },
      {
        user: "Claudia",
        avatar: "https://i.pravatar.cc/150?img=50",
        rating: 4,
        comment:
          "Un deportivo icónico que equilibra perfectamente potencia y elegancia."
      }
    ]
  },
  {
    id: 24,
    name: "McLaren 570S",
    type: "Superdeportivo",
    year: 2021,
    brand: "mclaren",
    plan: "elite",
    image:
      "https://www.charlottemclaren.com/wp-content/uploads/2018/11/MCLAREN-570S.jpg",
    power: "570 CV",
    acceleration: "3.2s",
    traction: "RWD",
    details: {
      description:
        "El McLaren 570S es un superdeportivo diseñado para ofrecer una experiencia de conducción extrema y un diseño futurista.",
      specs: [
        { name: "Potencia", value: "570 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "3.2s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "RWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Automática",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://www.charlottemclaren.com/wp-content/uploads/2018/11/MCLAREN-570S.jpg",
        "https://www.charlottemclaren.com/wp-content/uploads/2018/11/MCLAREN-570S.jpg",
        "https://www.charlottemclaren.com/wp-content/uploads/2018/11/MCLAREN-570S.jpg",
        "https://www.charlottemclaren.com/wp-content/uploads/2018/11/MCLAREN-570S.jpg"
      ]
    },
    reviews: [
      {
        user: "Sergio",
        avatar: "https://i.pravatar.cc/150?img=51",
        rating: 5,
        comment:
          "Una explosión de adrenalina en cada aceleración. Un auto que te hace vibrar."
      },
      {
        user: "Teresa",
        avatar: "https://i.pravatar.cc/150?img=52",
        rating: 4,
        comment:
          "Futurista y potente, aunque el confort en viajes largos podría mejorar."
      }
    ]
  },
  {
    id: 25,
    name: "Nissan GT-R Nismo",
    type: "Superdeportivo",
    year: 2022,
    brand: "nissan",
    plan: "elite",
    image:
      "https://car-images.bauersecure.com/wp-images/13098/2022-gt-r-nismo-07.jpg",
    power: "600 CV",
    acceleration: "2.5s",
    traction: "AWD",
    details: {
      description:
        "El Nissan GT-R Nismo es una bestia en carretera, reconocido por su aceleración vertiginosa y diseño agresivo.",
      specs: [
        { name: "Potencia", value: "600 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "2.5s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "AWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Automática",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://car-images.bauersecure.com/wp-images/13098/2022-gt-r-nismo-07.jpg",
        "https://car-images.bauersecure.com/wp-images/13098/2022-gt-r-nismo-07.jpg",
        "https://car-images.bauersecure.com/wp-images/13098/2022-gt-r-nismo-07.jpg",
        "https://car-images.bauersecure.com/wp-images/13098/2022-gt-r-nismo-07.jpg"
      ]
    },
    reviews: [
      {
        user: "Germán",
        avatar: "https://i.pravatar.cc/150?img=53",
        rating: 5,
        comment:
          "Una auténtica bestia en carretera, con aceleración impresionante y diseño agresivo."
      },
      {
        user: "Rosa",
        avatar: "https://i.pravatar.cc/150?img=54",
        rating: 4,
        comment:
          "Una ingeniería fuera de serie, aunque su precio solo es para los más exigentes."
      }
    ]
  },
  {
    id: 26,
    name: "Aston Martin Vantage",
    type: "GT Deportivo",
    year: 2020,
    brand: "aston-martin",
    plan: "elite",
    image:
      "https://www.motortrend.com/uploads/sites/11/2020/07/2020-Aston-Martin-Vantage-front-three-quarter-1.jpg",
    power: "503 CV",
    acceleration: "3.6s",
    traction: "RWD",
    details: {
      description:
        "El Aston Martin Vantage es un GT deportivo que combina lujo, elegancia y un rendimiento refinado.",
      specs: [
        { name: "Potencia", value: "503 CV", icon: "bi-lightning-charge" },
        { name: "0-100 km/h", value: "3.6s", icon: "bi-speedometer" },
        { name: "Velocidad máxima", value: "N/D", icon: "bi-trophy" },
        { name: "Tracción", value: "RWD", icon: "bi-gear" },
        {
          name: "Transmisión",
          value: "Automática",
          icon: "bi-arrows-fullscreen"
        },
        { name: "Consumo", value: "N/D", icon: "bi-fuel-pump" }
      ],
      images: [
        "https://www.motortrend.com/uploads/sites/11/2020/07/2020-Aston-Martin-Vantage-front-three-quarter-1.jpg",
        "https://www.motortrend.com/uploads/sites/11/2020/07/2020-Aston-Martin-Vantage-front-three-quarter-1.jpg",
        "https://www.motortrend.com/uploads/sites/11/2020/07/2020-Aston-Martin-Vantage-front-three-quarter-1.jpg",
        "https://www.motortrend.com/uploads/sites/11/2020/07/2020-Aston-Martin-Vantage-front-three-quarter-1.jpg"
      ]
    },
    reviews: [
      {
        user: "Diego",
        avatar: "https://i.pravatar.cc/150?img=55",
        rating: 4,
        comment:
          "Elegante y refinado, el Vantage es perfecto para los que buscan lujo con deportividad."
      },
      {
        user: "Silvia",
        avatar: "https://i.pravatar.cc/150?img=56",
        rating: 3,
        comment:
          "Un GT deportivo con estilo, aunque esperaba un poco más de potencia en el acelerado."
      }
    ]
  }
];


// Initialize vehicles page functionality
document.addEventListener('DOMContentLoaded', function() {
  // Check if we're on the vehicles page
  const vehiclesContainer = document.getElementById('vehiclesContainer');
  
  if (vehiclesContainer) {
    // Render all vehicles initially
    renderVehicles(vehiclesData);
    
    // Set up filter functionality
    const applyFiltersBtn = document.getElementById('applyFilters');
    
    if (applyFiltersBtn) {
      applyFiltersBtn.addEventListener('click', function() {
        const categoryFilter = document.getElementById('categoryFilter').value;
        const brandFilter = document.getElementById('brandFilter').value;
        const planFilter = document.getElementById('planFilter').value;
        
        const filteredVehicles = vehiclesData.filter(vehicle => {
          const matchCategory = !categoryFilter || vehicle.type.toLowerCase().includes(categoryFilter.toLowerCase());
          const matchBrand = !brandFilter || vehicle.brand.toLowerCase() === brandFilter.toLowerCase();
          const matchPlan = !planFilter || vehicle.plan.toLowerCase() === planFilter.toLowerCase();
          
          return matchCategory && matchBrand && matchPlan;
        });
        
        renderVehicles(filteredVehicles);
      });
    }
  }
  
  // Check if we're on the vehicle detail page
  const vehicleImagesContainer = document.getElementById('vehicleImagesContainer');
  const technicalSpecsContainer = document.getElementById('technicalSpecsContainer');
  const reviewsContainer = document.getElementById('reviewsContainer');
  const similarVehiclesContainer = document.getElementById('similarVehiclesContainer');
  
  if (vehicleImagesContainer && technicalSpecsContainer) {
    // Get vehicle ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const vehicleId = parseInt(urlParams.get('id'));
    
    if (vehicleId) {
      const vehicle = vehiclesData.find(v => v.id === vehicleId);
      
      if (vehicle) {
        // Set vehicle details
        document.getElementById('vehicleType').textContent = vehicle.type;
        document.getElementById('vehicleName').textContent = vehicle.name;
        document.getElementById('vehicleYear').textContent = vehicle.year;
        document.getElementById('vehiclePlan').textContent = `Plan ${vehicle.plan.charAt(0).toUpperCase() + vehicle.plan.slice(1)}`;
        document.getElementById('vehicleDescription').textContent = vehicle.details.description;
        document.title = `${vehicle.name} | DriveClub - Alquiler de Coches por Suscripción`;
        
        // Render images
        vehicle.details.images.forEach((image, index) => {
          const carouselItem = document.createElement('div');
          carouselItem.className = index === 0 ? 'carousel-item active' : 'carousel-item';
          
          const img = document.createElement('img');
          img.src = image;
          img.className = 'd-block w-100';
          img.alt = vehicle.name;
          
          carouselItem.appendChild(img);
          vehicleImagesContainer.appendChild(carouselItem);
        });
        
        // Render technical specs
        vehicle.details.specs.forEach(spec => {
          const specCol = document.createElement('div');
          specCol.className = 'col-md-4 col-6';
          
          specCol.innerHTML = `
            <div class="spec-card">
              <div class="spec-icon">
                <i class="bi ${spec.icon}"></i>
              </div>
              <div class="spec-title">${spec.name}</div>
              <div class="spec-value">${spec.value}</div>
            </div>
          `;
          
          technicalSpecsContainer.appendChild(specCol);
        });
        
        // Render reviews
        if (reviewsContainer && vehicle.reviews) {
          vehicle.reviews.forEach(review => {
            const reviewCard = document.createElement('div');
            reviewCard.className = 'review-card';
            
            const stars = '★'.repeat(review.rating) + '☆'.repeat(5 - review.rating);
            
            reviewCard.innerHTML = `
              <div class="reviewer-info">
                <img src="${review.avatar}" alt="${review.user}">
                <div>
                  <h5>${review.user}</h5>
                  <div class="rating">${stars}</div>
                </div>
              </div>
              <div class="review-content">
                <p>${review.comment}</p>
              </div>
            `;
            
            reviewsContainer.appendChild(reviewCard);
          });
        }
        
        // Render similar vehicles
        if (similarVehiclesContainer) {
          const similarVehicles = vehiclesData
            .filter(v => v.id !== vehicleId && (v.type === vehicle.type || v.brand === vehicle.brand))
            .slice(0, 3);
          
          similarVehicles.forEach(vehicle => {
            const col = document.createElement('div');
            col.className = 'col-lg-4 col-md-6';
            
            col.innerHTML = `
              <div class="vehicle-card">
                <img src="${vehicle.image}" alt="${vehicle.name}" class="vehicle-img">
                <div class="vehicle-content">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <h3 class="vehicle-name">${vehicle.name}</h3>
                    <span class="vehicle-year">${vehicle.year}</span>
                  </div>
                  <span class="vehicle-type mb-3">${vehicle.type}</span>
                  <div class="specs mb-4">
                    <div class="spec-item">
                      <span class="spec-name">Potencia</span>
                      <span class="spec-value">${vehicle.power}</span>
                    </div>
                    <div class="spec-item">
                      <span class="spec-name">0-100 km/h</span>
                      <span class="spec-value">${vehicle.acceleration}</span>
                    </div>
                    <div class="spec-item">
                      <span class="spec-name">Tracción</span>
                      <span class="spec-value">${vehicle.traction}</span>
                    </div>
                  </div>
                  <div class="plan-badge mb-3">Plan ${vehicle.plan.charAt(0).toUpperCase() + vehicle.plan.slice(1)}</div>
                  <a href="/vehiculo.html?id=${vehicle.id}" class="btn btn-primary w-100">Ver Detalles</a>
                </div>
              </div>
            `;
            
            similarVehiclesContainer.appendChild(col);
          });
        }
      }
    }
  }
});

// Function to render vehicles
function renderVehicles(vehicles) {
  const vehiclesContainer = document.getElementById('vehiclesContainer');
  const filterErrorContainer = document.getElementById('filterErrorContainer');
  
  // Clear previous vehicles
  vehiclesContainer.innerHTML = '';
  
  // Show/hide error message based on results
  if (vehicles.length === 0) {
    filterErrorContainer.style.display = 'block';
    return;
  } else {
    filterErrorContainer.style.display = 'none';
  }
  
  // Render each vehicle
  vehicles.forEach(vehicle => {
    const col = document.createElement('div');
    // Change column classes to provide better responsive behavior
    col.className = 'col-sm-12 col-md-6 col-lg-4 col-xl-3';
    
    col.innerHTML = `
      <div class="vehicle-card">
        <img src="${vehicle.image}" alt="${vehicle.name}" class="vehicle-img">
        <div class="vehicle-content">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h3 class="vehicle-name">${vehicle.name}</h3>
            <span class="vehicle-year">${vehicle.year}</span>
          </div>
          <span class="vehicle-type mb-3">${vehicle.type}</span>
          <div class="specs mb-4">
            <div class="spec-item">
              <span class="spec-name">Potencia</span>
              <span class="spec-value">${vehicle.power}</span>
            </div>
            <div class="spec-item">
              <span class="spec-name">0-100 km/h</span>
              <span class="spec-value">${vehicle.acceleration}</span>
            </div>
            <div class="spec-item">
              <span class="spec-name">Tracción</span>
              <span class="spec-value">${vehicle.traction}</span>
            </div>
          </div>
          <div class="plan-badge mb-3">Plan ${vehicle.plan.charAt(0).toUpperCase() + vehicle.plan.slice(1)}</div>
          <a href="/public/vehiculo.html?id=${vehicle.id}" class="btn btn-primary w-100">Ver Detalles</a>
        </div>
      </div>
    `;
    
    vehiclesContainer.appendChild(col);
  });
}