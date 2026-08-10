import { useState, useEffect } from 'react';
import axios from 'axios';
import PostList from './PostList';
import './App.css';

function App() {
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    axios
      .get('http://localhost/api/posts')
      .then((response) => {
        setPosts(response.data.data);
        setLoading(false);
      })
      .catch((err) => {
        console.error(err);
        setError('投稿の取得に失敗しました。');
        setLoading(false);
      });
  }, []);

  if (loading) return <p>読み込み中...</p>;
  if (error) return <p>{error}</p>;

  return (
    <div style={{ maxWidth: '700px', margin: '0 auto', padding: '20px' }}>
      <h1>投稿一覧</h1>
      <PostList posts={posts} />
    </div>
  );
}

export default App;