test('promise testing', async () => {
  const pro = new Promise((resolve) => {
    setTimeout(() => {
      resolve(5);
      }, 50
    );
  });
  return pro.then(data => {
    expect(data).toBe(5);
  })
});

test('async testing', async () => {
  const fetchData = new Promise((resolve) => {
    setTimeout(() => {
        resolve(6);
      }, 50
    );
  });
  const data = await fetchData;
  expect(data).toBe(6);
});


