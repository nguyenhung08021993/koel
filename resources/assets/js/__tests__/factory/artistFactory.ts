import { Faker } from '@faker-js/faker'

export default (faker: Faker): Artist => {
  const length = faker.datatype.number({ min: 300 })

  return {
    type: 'artists',
    id: faker.datatype.number({ min: 3 }), // avoid Unknown and Various Artist by default
    name: faker.name.findName(),
    image: 'foo.jpg',
    play_count: faker.datatype.number(),
    album_count: faker.datatype.number({ max: 10 }),
    song_count: faker.datatype.number({ max: 100 }),
    length,
    created_at: faker.date.past().toISOString()
  }
}

export const states: Record<string, Omit<Partial<Artist>, 'type'>> = {
  unknown: {
    id: 1,
    name: 'Unknown Artist'
  },
  various: {
    id: 2,
    name: 'Various Artists'
  }
}
