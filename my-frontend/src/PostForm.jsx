import { useState } from 'react';
import axios from 'axios';

function PostForm({ onPostCreated }) {
  const [title, setTitle] = useState('');
  const [body, setBody] = useState('');
  const [categoryId, setCategoryId] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState(null);

  const handleSubmit = (e) => {
    e.preventDefault();
    setSubmitting(true);
    setError(null);

    axios
      .post('http://localhost/api/posts', {
        title,
        body,
        category_id: categoryId,
      })
      .then(() => {
        setTitle('');
        setBody('');
        setCategoryId('');
        onPostCreated();
      })
      .catch((err) => {
        console.error(err);
        setError('投稿の作成に失敗しました。入力内容を確認してください。');
      })
      .finally(() => {
        setSubmitting(false);
      });
  };

  return (
    <form onSubmit={handleSubmit} style={{ marginBottom: '24px', border: '1px solid #ccc', borderRadius: '8px', padding: '16px' }}>
      <h2>新規投稿</h2>

      {error && <p style={{ color: 'red' }}>{error}</p>}

      <div style={{ marginBottom: '8px' }}>
        <label>タイトル<br />
          <input
            type="text"
            value={title}
            onChange={(e) => setTitle(e.target.value)}
            required
            style={{ width: '100%' }}
          />
        </label>
      </div>

      <div style={{ marginBottom: '8px' }}>
        <label>本文<br />
          <textarea
            value={body}
            onChange={(e) => setBody(e.target.value)}
            required
            style={{ width: '100%' }}
          />
        </label>
      </div>

      <div style={{ marginBottom: '8px' }}>
        <label>カテゴリID<br />
          <input
            type="number"
            value={categoryId}
            onChange={(e) => setCategoryId(e.target.value)}
            required
          />
        </label>
      </div>

      <button type="submit" disabled={submitting}>
        {submitting ? '投稿中...' : '投稿する'}
      </button>
    </form>
  );
}

export default PostForm;