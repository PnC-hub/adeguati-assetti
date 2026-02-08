import mysql from 'mysql2/promise'

const pool = mysql.createPool({
  host: process.env.DB_HOST || '93.186.255.213',
  user: process.env.DB_USER || 'geniusmile',
  password: process.env.DB_PASSWORD || 'dI20mgnkINkQ4iRBOoQHl0gh',
  database: process.env.DB_NAME || 'geniusmile_production',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
})

const PREFIX = 'afts5498_aa_'

export async function query<T = any>(sql: string, params: any[] = []): Promise<T[]> {
  const [rows] = await pool.execute(sql, params)
  return rows as T[]
}

export async function findOne<T = any>(table: string, where: Record<string, any>): Promise<T | null> {
  const keys = Object.keys(where)
  const sql = `SELECT * FROM ${PREFIX}${table} WHERE ${keys.map(k => `${k} = ?`).join(' AND ')} LIMIT 1`
  const rows = await query<T>(sql, Object.values(where))
  return rows[0] || null
}

export async function findAll<T = any>(table: string, where: Record<string, any> = {}): Promise<T[]> {
  const keys = Object.keys(where)
  let sql = `SELECT * FROM ${PREFIX}${table}`
  if (keys.length > 0) {
    sql += ` WHERE ${keys.map(k => `${k} = ?`).join(' AND ')}`
  }
  return query<T>(sql, Object.values(where))
}

export async function insert(table: string, data: Record<string, any>): Promise<number> {
  const keys = Object.keys(data)
  const sql = `INSERT INTO ${PREFIX}${table} (${keys.join(', ')}) VALUES (${keys.map(() => '?').join(', ')})`
  const [result] = await pool.execute(sql, Object.values(data)) as any
  return result.insertId
}

export async function update(table: string, data: Record<string, any>, where: Record<string, any>): Promise<number> {
  const dataKeys = Object.keys(data)
  const whereKeys = Object.keys(where)
  const sql = `UPDATE ${PREFIX}${table} SET ${dataKeys.map(k => `${k} = ?`).join(', ')} WHERE ${whereKeys.map(k => `${k} = ?`).join(' AND ')}`
  const [result] = await pool.execute(sql, [...Object.values(data), ...Object.values(where)]) as any
  return result.affectedRows
}

export async function remove(table: string, where: Record<string, any>): Promise<number> {
  const keys = Object.keys(where)
  const sql = `DELETE FROM ${PREFIX}${table} WHERE ${keys.map(k => `${k} = ?`).join(' AND ')}`
  const [result] = await pool.execute(sql, Object.values(where)) as any
  return result.affectedRows
}

export { pool, PREFIX }
